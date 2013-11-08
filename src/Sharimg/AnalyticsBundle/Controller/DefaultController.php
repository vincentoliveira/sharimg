<?php

namespace Sharimg\AnalyticsBundle\Controller;

use Sharimg\DefaultBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Logger default controller
 */
class DefaultController extends BaseController
{
    /**
     * @Template
     * @Secure(roles="ROLE_ADMIN")
     * @return Response
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        
        $dateFrom = $dateTo = '';
        $from = $to = null;
        if ($request->getMethod() == 'POST') {
            $dateFrom = $request->request->get('date_from');
            if (!empty($dateFrom)) {
                $from = new \DateTime($request->request->get('date_from'));
            }
            $dateTo = $request->request->get('date_to');
            if (!empty($dateTo)) {
                $to = new \DateTime($request->request->get('date_to'));
            }
        }
        $logger = $this->get('sharimg_analytics.logger_service');
        $files = $logger->getLogFiles($from, $to);
        $webDirectory = $logger->getWebDirectory();
        
        return array(
            'files' => $files,
            'webDirectory' => $webDirectory,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        );
    }
    
    /**
     * @Template
     * @Secure(roles="ROLE_ADMIN")
     * @param string $logfile
     * @return Response
     */
    public function logsAction($logfile)
    {
        $logger = $this->get('sharimg_analytics.logger_service');
        $analytics = $this->get('sharimg_analytics.analytics_service');
        $webDirectory = $logger->getWebDirectory();
        $analyse = $analytics->analyseLogs($logfile);
        
        $logs = $analyse['logs'];
        $connections = $analyse['connections'];
        $pages = $analyse['pages'];
        $visitors = $analyse['visitors'];
        
        $nbLogs = count($logs);
        $uniqueVisitors = count($visitors);
        $averagePages = $nbLogs / $uniqueVisitors;
        
        $offset = 0;
        $length = 10;
        
        arsort($pages, SORT_NUMERIC);
        $sortedSlicedPages = array_splice($pages, $offset, $length);
        arsort($visitors, SORT_NUMERIC);
        $sortedSlicedVisitors = array_splice($visitors, $offset, $length);
        
        return array(
            'logfile' => $logfile,
            'webDirectory' => $webDirectory,
            'connections' => $connections,
            'pages' => $sortedSlicedPages,
            'visitors' => $sortedSlicedVisitors,
            'uniqueVisitors' => $uniqueVisitors,
            'averagePages' => $averagePages,
        );
    }
}
