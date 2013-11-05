<?php

namespace Sharimg\ImportBundle\Service;

use Symfony\Component\DependencyInjection\Container;

/**
 * TwitterApiManager: Handle calls to Twitter API 
 */
class TwitterApiManager
{

    protected $container;

    /**
     * Constructor
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }


    /**
     * Get $screenName timeline
     * @param string $screenName
     * @return array List of tweets
     */
    public function getUserTimeline($screenName)
    {
        $count = $this->container->getParameter('twitter_api_manager.tweet_count');
        $consumerKey = $this->container->getParameter('twitter_api_manager.consumer_key');
        $consumerSecret = $this->container->getParameter('twitter_api_manager.consumer_secret');
        $oauthToken = $this->container->getParameter('twitter_api_manager.oauth_token');
        $oauthSecret = $this->container->getParameter('twitter_api_manager.oauth_secret');
        $apiUrl = $this->container->getParameter('twitter_api_manager.usertimeline_url');

        $connection = new \TwitterOAuth($consumerKey, $consumerSecret, $oauthToken, $oauthSecret);
        
        $parameters = array();
        $parameters['screen_name'] = $screenName;
        $parameters['count'] = $count;
        $parameters['contributor_details'] = false;
        $timelineArray = $connection->get($apiUrl, $parameters);
        
        if ($timelineArray === null || !is_array($timelineArray)) {
            return array();
        }
        
        $timeline = array();
        foreach ($timelineArray as $tweet) {
            $tweetData = array();
            $tweetData['text'] = $tweet->text;
            $tweetData['author'] = $tweet->user->screen_name;
            $tweetData['title'] = $this->getTitleFromTweet($tweet->text);
            
            $media = isset($tweet->entities->media) ? $tweet->entities->media : null;
            if (!empty($media)) {
                $tweetData['image'] = $media[0]->media_url;
            } else {
                $tweetData['image'] = null;                
            }
            
            $timeline[] = $tweetData;
        }
        
        return $timeline;
    }


    /**
     * Get $screenName home timeline
     * @param string $screenName
     * @return array List of tweets
     */
    public function getHomeTimeline($screenName)
    {
        $count = $this->container->getParameter('twitter_api_manager.tweet_count');
        $consumerKey = $this->container->getParameter('twitter_api_manager.consumer_key');
        $consumerSecret = $this->container->getParameter('twitter_api_manager.consumer_secret');
        $oauthToken = $this->container->getParameter('twitter_api_manager.oauth_token');
        $oauthSecret = $this->container->getParameter('twitter_api_manager.oauth_secret');
        $apiUrl = $this->container->getParameter('twitter_api_manager.hometimeline_url');

        $connection = new \TwitterOAuth($consumerKey, $consumerSecret, $oauthToken, $oauthSecret);
        
        $parameters = array();
        $parameters['screen_name'] = $screenName;
        $parameters['count'] = $count;
        $parameters['contributor_details'] = false;
        $timelineArray = $connection->get($apiUrl, $parameters);
        
        if ($timelineArray === null || !is_array($timelineArray)) {
            return array();
        }
        
        $timeline = array();
        foreach ($timelineArray as $tweet) {
            $tweetData = array();
            $tweetData['text'] = $tweet->text;
            $tweetData['author'] = $tweet->user->screen_name;
            $tweetData['title'] = $this->getTitleFromTweet($tweet->text);
            
            $media = isset($tweet->entities->media) ? $tweet->entities->media : null;
            if (!empty($media)) {
                $tweetData['image'] = $media[0]->media_url;
            } else {
                $tweetData['image'] = null;
            }
            
            $timeline[] = $tweetData;
        }
        
        return $timeline;
    }

    /**
     * Remove url from tweet to get title
     * 
     * @param string $tweet
     * @return string
     */
    protected function getTitleFromTweet($tweet)
    {
        $pattern = "/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i";
        $replacement = "";
        return preg_replace($pattern, $replacement, $tweet);
    }

}