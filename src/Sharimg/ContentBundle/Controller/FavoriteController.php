<?php

namespace Sharimg\ContentBundle\Controller;

use Sharimg\DefaultBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * DefaultController
 */
class FavoriteController extends BaseController
{
    /**
     * Homepage
     * 
     * @Template
     * @Secure(roles="ROLE_USER")
     * @return Response
     */
    public function listAction()
    {
        $user = $this->getCurrentUser();
        $favoriteService = $this->get('sharimg_content.favorite_service');
        return $favoriteService->getList($user);
    }
    
    /**
     * Favorize webservice
     * 
     * @Secure(roles="ROLE_USER")
     * @return Response
     */
    public function favorizeWSAction()
    {
        $favoriteService = $this->get('sharimg_content.favorite_service');
        $errors = $favoriteService->favorize();
        
        return new JsonResponse(array('favorize' => !is_array($errors)));
    }
}
