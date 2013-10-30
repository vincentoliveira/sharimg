<?php

namespace Sharimg\ContentBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Sharimg\ContentBundle\Entity\Content;

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
                (!isset($params['media_url']) or empty($params['media_url']))) {
            $errors['file_input'][] = 'content.error.empty_file_input';
            $errors['media_url'][] = 'content.error.empty_media_url';
        }
        
        if ((isset($params['file_input']) and !empty($params['file_input'])) &&
                (isset($params['media_url']) and !empty($params['media_url']))) {
            $errors['file_input'][] = 'content.error.choose_one_content';
            $errors['media_url'][] = 'content.error.choose_one_content';
        }
        
        if (!isset($params['description']) or empty($params['description'])) {
            $errors['description'][] = 'content.error.description_must_be_filled';
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
        try {
            $content = new Content();

            $content->setDescription($this->getPostParams($params, 'description'));
            $content->setSource($this->getPostParams($params, 'source'));
            $content->setVisible(isset($params['is_visible']));

            // set date
            try {
                $date = new \DateTime($this->getPostParams($params, 'date'));
            } catch (\Exception $e) {
                $date = new \DateTime();
            }
            $content->setDate($date);

            $mediaHandler = $this->container->get('sharimg_content.media_handler');
            
            // media
            $mediaUrl = $this->getPostParams($params, 'media_url');
            if (!empty($mediaUrl)) {
                $media = $mediaHandler->createMediaFromUrl($mediaUrl);
                $content->setMedia($media);
            }
            else {
                $inputFile = $this->getPostParams($params, 'file_input');
                $media = $mediaHandler->createMediaFromFile($inputFile);
                $content->setMedia($media);
            }
            
            $this->em->persist($content);
            $this->em->flush();
            
            return $content->getId();
        }
        catch (\Exception $e) {
            throw $e;
            return false;            
        }
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
