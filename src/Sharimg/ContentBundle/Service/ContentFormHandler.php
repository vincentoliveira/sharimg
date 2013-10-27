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
     * Check if POST $params are valid
     * @param array $params
     * @return array erros or true if params are valid
     */
    public function isValid(Array $params)
    {
        $errors = array();
        if ((!isset($params['file_input']) or empty($params['file_input'])) &&
                (!isset($params['content_url']) or empty($params['content_url']))) {
            $errors['file_input'][] = 'content.error.empty_file_input';
            $errors['content_url'][] = 'content.error.empty_content_url';
        }
        
        if ((isset($params['file_input']) and !empty($params['file_input'])) &&
                (isset($params['content_url']) and !empty($params['content_url']))) {
            $errors['file_input'][] = 'content.error.choose_one_content';
            $errors['content_url'][] = 'content.error.choose_one_content';
        }
        
        return empty($errors) ? true : $errors;
    }

    /**
     * Add content from form
     * @param array $rowContent
     * @return int New content id
     */
    public function hydrateEntity(Array $params)
    {
        $content = new Content();
        
        $content->setDescription($this->getPostParams($params, 'description'));
        $content->setVisible($this->getPostParams($params, 'is_visible'));
        $content->setFrom($this->getPostParams($params, 'from'));
        
        // set date
        try {
            $date = new \DateTime($this->getPostParams($params, 'date'));
        } catch (\Exception $e) {
            $date = new \DateTime();
        }
        $content->setDate($date);
        
        // content
        $contentUrl = $this->getPostParams($params, 'content_url');
        if (!empty($contentUrl)) {
            $filename = sha1(uniqid(mt_rand(), true));
            $path = '/tmp/' . $filename;
            
            $ch = curl_init($contentUrl);
            $fp = fopen($path, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            
            $content->setTmpPath($path);
        }
        else {
            $content->setTmpFile($this->getPostParams($params, 'file_input'));
        }
        
        $this->em->persist($content);
        
        try {
            $this->em->flush();
        } catch (\Exception $e) {
            return false;
        }
        
        return $content->getId();
    }
    
    /**
     * Get POST parameter or default value
     * @param type $params
     * @param type $name
     * @return string
     */
    protected function getPostParams($params, $name)
    {
        return isset($params[$name]) ? $params[$name] : '';
    }
}
