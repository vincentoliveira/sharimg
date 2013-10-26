<?php

namespace Sharimg\ContentBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Sharimg\DefaultBundle\Entity\Content;

/**
 * ContentFormHandler: Handle content form
 */
class ContentFormHandler
{
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * Add content from form
     * @param array $rowContent
     * @return int New content id
     */
    public function add($rowContent)
    {
        $content = new Content();
        
        $content->setDate($rowContent['date']);
        $content->setDescription($rowContent['description']);
        $content->setVisible($rowContent['visible']);
        
        if (!empty($rowContent['url'])) {
            $filename = sha1(uniqid(mt_rand(), true));
            $path = '/tmp/' . $filename;
            
            $ch = curl_init($rowContent['url']);
            $fp = fopen($path, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            
            $content->tmpPath = $path;
        }
        else {
            $content->file = $rowContent['file'];            
        }
        
        $this->em->persist($content);
        $this->em->flush();
        
        return $content->getId();
    }
}
