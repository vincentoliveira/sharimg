<?php

namespace Sharimg\ContentBundle\Controller;

use Sharimg\DefaultBundle\Controller\ApiController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * FavoriteApiController
 */
class FavoriteApiController extends BaseController
{
    /**
     * Get favorize list 
     * 
     * @return Response
     */
    public function listAction()
    {
        $user = $this->authentificate();
        
        $favoriteService = $this->get('sharimg_content.favorite_service');
        return new JsonResponse($favoriteService->getList($user));
    }
    
    /**
     * Favorize
     * 
     * @return Response
     */
    public function favorizeAction()
    {
        $this->authentificate();
        
        $favoriteService = $this->get('sharimg_content.favorite_service');
        return new JsonResponse(array('favorize' => $favoriteService->favorize()));
    }
}
