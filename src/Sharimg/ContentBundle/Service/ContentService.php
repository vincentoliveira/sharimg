<?php

namespace Sharimg\ContentBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Sharimg\ContentBundle\Entity\Content;
use Sharimg\DefaultBundle\Controller\ApiController;

/**
 * ContentService
 */
class ContentService
{
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
    
    /**
     * Update the status of a content
     * @param array $params Post parameters
     * @return array errors or true if success
     */
    public function moderate(Array $params)
    {
        $errors = array();
        $contentId = $this->getParameter($params, 'content_id');
        $statusId = $this->getParameter($params, 'status_id');
        $description = $this->getParameter($params, 'description');
        
        $statuses = $this->container->getParameter('content.status');
        if (!isset($statuses[$statusId])) {
            $errors[ApiController::ERROR_INVALID_ARGS] = array();
            $errors[ApiController::ERROR_INVALID_ARGS][] = 'status_id';
        }
        
        $content = $this->em->getRepository('SharimgContentBundle:Content')->find($contentId);
        if ($content === null) {
            if (!isset($errors[ApiController::ERROR_INVALID_ARGS])) {
                $errors[ApiController::ERROR_INVALID_ARGS] = array();
            }
            $errors[ApiController::ERROR_INVALID_ARGS][] = 'content_id';
        }
        
        if (!empty ($errors)) {
            return $errors;
        }
        
        try {
            if (!empty($description)) {
                $content->setDescription($description);
            }
            $content->setStatusId($statusId);
            $this->em->persist($content);
            $this->em->flush();
            return true;
        } catch (\Exception $e) {
            return array(ApiController::ERROR_INTERNAL => $e->getMessage());
        }
    }
    
    /**
     * Get POST parameter or default value
     * @param type $params
     * @param type $name
     * @return string
     */
    protected function getParameter($params, $name)
    {
        return isset($params[$name]) ? $params[$name] : '';
    }
}
