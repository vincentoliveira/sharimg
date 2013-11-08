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
        
        $data = array(
            $date->format($datetimeFormat),
            $request->get('_route'),
            $request->getRequestUri(),
            $request->getClientIp(),
            str_replace(';', ',', $request->headers->get('User-Agent')),
        );
        $dataStr = implode(';', $data)."\n";
        
        try {
            $logFilePointer = fopen($logFilename, 'a');
            fwrite($logFilePointer, $dataStr);
            fclose($logFilePointer);
        } catch(\Exception $e) {
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
            
        } catch(\Exception $e) {
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
        return $uploadDir . (substr($uploadDir, -1) === '/' ? '' : '/');die;
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
}