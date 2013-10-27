<?php

namespace Sharimg\ContentBundle\Controller;

use Sharimg\DefaultBundle\Controller\ApiController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $errors = array();
        
        $formHandler = $this->container->get('sharimg_content.content_form_handler');
        $isValid = $formHandler->isValid($params);
        if ($isValid === true) {
            $contentId = $formHandler->hydrateEntity($params);
            if ($contentId !== false) {
                return new JsonResponse(array($this->trans('api.content') => $contentId));
            } else {
                return new JsonResponse($this->error(self::ERROR_INTERNAL));
            }
        }
        
        return new JsonResponse($this->error(self::ERROR_BAD_ARG));
    }
    
    
    /**
     * View content details
     * 
     * @return Response
     */
    public function contentAction()
    {
        $contentId = $this->getRequest()->query->get('content_id');
        if (empty($contentId)) {
            return new JsonResponse($this->error(self::ERROR_BAD_ARG));
        }
        
        $content = $this->getRepository('Content')->getContentDetails(intval($contentId));
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
        $list = $this->getRepository('Content')->getVisibleList($params);
        
        return new JsonResponse(array($this->trans('api.contents') => $list));
    }
}
