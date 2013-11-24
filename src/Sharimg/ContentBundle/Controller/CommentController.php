<?php

namespace Sharimg\ContentBundle\Controller;

use Sharimg\DefaultBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * CommentController
 */
class CommentController extends BaseController
{
    /**
     * List
     * 
     * @Template
     * @param int $content content id
     * @return Response
     */
    public function listRawAction($content)
    {
        $service = $this->get('sharimg_content.comment_service');
        return array('comments' => $service->getList($content));
    }


    /**
     * Post a comment
     * 
     * @Template
     * @Secure(roles="ROLE_USER")
     * @param int $content content id
     * @return Response
     */
    public function postAction($content)
    {
        $service = $this->get('sharimg_content.comment_service');
        $user = $this->getUser();
        
        return $this->redirectToRoute('sharimg_content_show', array(
                    'content' => $content,
                    'post_comments' => $service->post($user, $content))
        );
    }


}
