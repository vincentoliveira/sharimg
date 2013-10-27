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
}
