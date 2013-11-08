<?php

namespace Sharimg\UserBundle\Controller;

use Sharimg\DefaultBundle\Controller\ApiController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * UserBundle ApiController
 */
class ApiController extends BaseController
{
    /**
     * Profile action
     * 
     * @return JsonResponse
     */
    public function loginAction()
    {
        return new JsonResponse(array('login' => array('success' => false)));
    }
    
    /**
     * Profile action
     * 
     * @return JsonResponse
     */
    public function registerAction()
    {
        $request = $this->getRequest();
        $postParams = $request->request->all();
        $userService = $this->get('sharimg_user.user_service');
        
        $results = $userService->register($postParams);
        if (isset($results['errors'])) {
            if (isset($results['errors']['missing_params'])) {
                $missingParams = array('%missing_params%' => implode(', ', $results['errors']['missing_params']));
                return $this->error(self::ERROR_MISSING_PARAMS, $missingParams);
            } else if (isset($results['errors']['email_format'])) {
                return $this->error(self::ERROR_EMAIL_FORMAT);
            } else if (isset($results['errors']['unique_parameters'])) {
                $uniqueParams = array('%unique_params%' => implode(', ', $results['errors']['unique_parameters']));
                return $this->error(self::ERROR_UNIQUE_PARAMS, $uniqueParams);
            } else {
                return $this->error(self::ERROR_INTERNAL);
            }
        }
        
        return new JsonResponse(array('register' => $results));
    }
}
