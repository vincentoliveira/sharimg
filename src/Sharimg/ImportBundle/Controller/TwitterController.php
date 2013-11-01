<?php

namespace Sharimg\ImportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class TwitterController extends Controller
{
    /**
     * @Template("SharimgImportBundle:Twitter:timeline.html.twig")
     * Get user timeline of $screenName
     * @param string $screenName
     */
    public function userTimelineAction($screenName)
    {
        $twitterManager = $this->container->get('sharimg_import.twitter_api_manager');
        
        $timeline = $twitterManager->getUserTimeline($screenName);
        
        return array(
            'screenName' => $screenName,
            'timeline' => $timeline,
        );
    }
    
    /**
     * @Template("SharimgImportBundle:Twitter:timeline.html.twig")
     * Get home timeline of $screenName
     * @param string $screenName
     */
    public function homeTimelineAction($screenName)
    {
        $twitterManager = $this->container->get('sharimg_import.twitter_api_manager');
        
        $timeline = $twitterManager->getHomeTimeline($screenName);
        
        return array(
            'screenName' => $screenName,
            'timeline' => $timeline,
        );
    }
}
