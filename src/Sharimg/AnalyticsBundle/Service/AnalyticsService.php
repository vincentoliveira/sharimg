<?php

namespace Sharimg\AnalyticsBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Analytics Service
 */
class AnalyticsService extends Controller
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
     * Analyse logs
     * @param string $logfile log filename
     * @return array Logs
     */
    public function analyseLogs($logfile)
    {
        $loggerService = $this->container->get('sharimg_analytics.logger_service');
        $logs = $loggerService->getLogs($logfile);
        
        // data
        $connections = array();
        $pages = array();
        $visitors = array();
        
        foreach ($logs as $log) {
            $hour = $log['date']->format('H');
            $connections[$hour] = isset($connections[$hour]) ? $connections[$hour] + 1 : 1;
            $pages[$log['url']] = isset($pages[$log['url']]) ? $pages[$log['url']] + 1 : 1;
            $visitors[$log['ip']] = isset($visitors[$log['ip']]) ? $visitors[$log['ip']] + 1 : 1;
        }
        
        return array(
            'logs' => $logs,
            'connections' => $connections,
            'pages' => $pages,
            'visitors' => $visitors,
        );
    }
}