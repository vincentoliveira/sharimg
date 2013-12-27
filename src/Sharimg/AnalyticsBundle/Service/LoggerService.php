<?php

namespace Sharimg\AnalyticsBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Logger Service
 */
class LoggerService extends Controller
{

    /**
     * Service container
     */
    protected $container = false;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Log a request
     * @return boolean false if something wrong appends, othercase true
     */
    public function log()
    {
        $request = $this->container->get('request');
        $date = new \DateTime();
        $logFilename = $this->getDirectory() . $this->getFilename($date);
        $datetimeFormat = $this->getDatetimeFormat();

        $ip = $request->getClientIp();

        $ctx = stream_context_create(array('http' => array('timeout' => 0.5)));
        $ip_data = @json_decode(file_get_contents('http://www.geoplugin.net/json.gp?ip=' . $ip, false, $ctx));
        
        $country = 'Unknown';
        if ($ip_data && $ip_data->geoplugin_countryName != null) {
            $country = $ip_data->geoplugin_countryName;
        }

        $userAgent = $request->headers->get('User-Agent');

        $data = array(
            $date->format($datetimeFormat),
            $request->get('_route'),
            $request->getRequestUri(),
            $ip,
            $country,
            str_replace(';', ',', $userAgent),
            $this->getBrowser($userAgent),
            $this->getOS($userAgent),
        );
        $dataStr = implode(';', $data) . "\n";

        try {
            $logFilePointer = fopen($logFilename, 'a');
            fwrite($logFilePointer, $dataStr);
            fclose($logFilePointer);
        } catch (\Exception $e) {
            //throw $e;
            return false;
        }

        return true;
    }

    /**
     * Get list of filename between $from and $to
     * @param DateTime $from (optionnal)
     * @param DateTime $to (optionnal)
     * return array of filename
     */
    public function getLogFiles(\DateTime $from = null, \DateTime $to = null)
    {
        if ($from) {
            $fromFilename = $this->getFilename($from);
        } else {
            $fromFilename = null;
        }
        if ($to) {
            $toFilename = $this->getFilename($to);
        } else {
            $toFilename = null;
        }

        $files = scandir($this->getDirectory(), SCANDIR_SORT_ASCENDING);
        $results = array();
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if ($fromFilename !== null && strcmp($fromFilename, $file) > 0) {
                continue;
            }
            if ($toFilename !== null && strcmp($toFilename, $file) < 0) {
                break;
            }
            $results[] = $file;
        }

        return $results;
    }

    /**
     * Get logs
     * @param string $logfile log filename
     * @return array Logs
     */
    public function getLogs($logfile)
    {
        $logs = array();
        try {
            $datetimeFormat = $this->getDatetimeFormat();
            $logpath = $this->getDirectory() . $logfile;
            $logsData = file($logpath);

            foreach ($logsData as $rawData) {
                $data = explode(';', $rawData);
                $namedData = array(
                    'date' => \DateTime::createFromFormat($datetimeFormat, $data[0]),
                    'route' => $data[1],
                    'url' => $data[2],
                    'ip' => $data[3],
                    'user_agent' => $data[4],
                );
                $logs[] = $namedData;
            }
        } catch (\Exception $e) {
            return $logs;
        }

        return $logs;
    }

    /**
     * Get web log directory
     * return string web log directory
     */
    public function getWebDirectory()
    {
        $uploadDir = $this->container->getParameter('analytics.log_directory');
        return $uploadDir . (substr($uploadDir, -1) === '/' ? '' : '/');
        die;
    }

    /**
     * return string log directory
     */
    protected function getDirectory()
    {
        $root = __DIR__ . '/../../../../web/';
        $uploadDir = $this->container->getParameter('analytics.log_directory');
        return (substr($uploadDir, 0, 1) === '/' ? '' : $root) . $uploadDir . (substr($uploadDir, -1) === '/' ? '' : '/');
    }

    /**
     * Return filename for $date
     * @param DateTime $date
     * @return string filename
     */
    protected function getFilename($date)
    {
        if ($date === null) {
            return '';
        }

        return $date->format('Ymd') . '.csv';
    }

    /**
     * Get datetime format
     * @return string datetime format
     */
    protected function getDatetimeFormat()
    {
        return 'm/d/Y H:i:s';
    }

    /**
     * Get Operating System
     * 
     * @param string $user_agent
     * @return string
     */
    private function getOS($user_agent)
    {
        $os_platform = "Unknown";

        $os_array = array(
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
        );

        foreach ($os_array as $regex => $value) {

            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }

        return $os_platform;
    }

    /**
     * Get browser
     * 
     * @param string $user_agent
     * @return string
     */
    private function getBrowser($user_agent)
    {
        $browser = "Unknown";

        $browser_array = array(
            '/msie/i' => 'Internet Explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/opera/i' => 'Opera',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i' => 'Handheld Browser'
        );

        foreach ($browser_array as $regex => $value) {

            if (preg_match($regex, $user_agent)) {
                $browser = $value;
            }
        }

        return $browser;
    }

}