<?php

namespace Sharimg\ContentBundle\Controller;

use Sharimg\DefaultBundle\Controller\ApiController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * ApiController
 */
class ApiController extends BaseController
{
    /**
     * Add content webservice
     * 
     * @return Response
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $postParams = $request->request->all();
        $fileParams = $request->files->all();
        $params = array_merge($postParams, $fileParams);
        
        $formHandler = $this->container->get('sharimg_content.content_form_handler');
        $isValid = $formHandler->isValid($params);
        if ($isValid === true) {
            $contentId = $formHandler->hydrateEntity($params);
            if ($contentId !== false) {
                return new JsonResponse(array($this->trans('api.content') => $contentId));
            } else {
                return $this->error(self::ERROR_INTERNAL);
            }
        }
        
        return $this->error(self::ERROR_BAD_ARG);
    }
    
    
    /**
     * View content details
     * 
     * @return Response
     */
    public function contentAction()
    {
        $random = $this->getRequest()->query->get('random');
        $contentId = $this->getRequest()->query->get('content_id');
        if ((empty($random) || $random === '0' || $random === 'f' || $random === 'false') && empty($contentId)) {
            return $this->error(self::ERROR_BAD_ARG);
        }
        
        if (!empty($contentId)) {
            $content = $this->getRepository('SharimgContentBundle:Content')->getContentDetails(intval($contentId));
        } else {
            $contentEntity = $this->getRepository('SharimgContentBundle:Content')->getRandom();
            $media = $contentEntity->getMedia();
            $content = array();
            $content['id'] = $contentEntity->getId();
            $content['date'] = $contentEntity->getDate();
            $content['description'] = $contentEntity->getDescription();
            $content['source'] = $contentEntity->getSource();
            $content['visible'] = $contentEntity->getVisible();
            $content['media'] = array();
            $content['media']['id'] = $media->getId();
            $content['media']['uploadDate'] = $media->getUploadDate();
            $content['media']['path'] = $media->getPath();
        }
        return new JsonResponse(array('content' => $content));
    }
    
    /**
     * Get content list
     */
    public function listAction()
    {
        $defaultParams = array(
            'count' => $this->container->getParameter('api.default.count'),
        );
        $params = array_merge($defaultParams, $this->getRequest()->query->all());
        $list = $this->getRepository('SharimgContentBundle:Content')->getVisibleList($params);
        
        return new JsonResponse(array($this->trans('api.contents') => $list));
    }
    
    /**
     * Update description of 'content_id' to 'description'
     * Update status of 'content_id' to 'status_id'
     * POST : status_id, content_id
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function moderateAction()
    {
        $postParameters = $this->getRequest()->request->all();
        $contentService = $this->container->get('sharimg_content.content_service');
        $results = $contentService->moderate($postParameters);
        
        if ($results !== true) {
            if (is_array($results)) {
                return $this->error2($results);
            } else {
                return $this->error(self::ERROR_INTERNAL);
            }
        }
        
        return new JsonResponse(array('update_status' => true));
    }
}
