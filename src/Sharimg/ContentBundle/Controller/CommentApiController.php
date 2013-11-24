<?php

namespace Sharimg\ContentBundle\Controller;

use Sharimg\DefaultBundle\Controller\ApiController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * CommentApiController
 */
class CommentApiController extends BaseController
{
    /**
     * List
     * 
     * @Template
     * @return Response
     */
    public function listAction()
    {
        $service = $this->get('sharimg_content.comment_service');
        return new JsonResponse(array('comments' => $service->getList()));
    }
    
    /**
     * Post a comment
     * 
     * @Template
     * @return Response
     */
    public function postAction()
    {
        $service = $this->get('sharimg_content.comment_service');
        $user = $this->authentificate();
        
        return new JsonResponse(array('post_comments' => $service->post($user)));
    }
}
