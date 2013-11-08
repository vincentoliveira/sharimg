<?php

namespace Sharimg\ContentBundle\Controller;

use Sharimg\DefaultBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sharimg\ContentBundle\Entity\Content;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Admin Controller
 */
class AdminController extends BaseController
{
    /**
     * Moderate content
     * 
     * @Template
     * @Secure(roles="ROLE_ADMIN")
     * @return Response
     */
    public function moderateAction()
    {
        $request = $this->getRequest();
        
        $first = 0;
        $maxResults = $this->container->getParameter('moderate.content.pagination');
        $page = $request->request->get('page');
        if (isset($page)) {
            $first = $maxResults * ($first - 1);
        }
        
        $contents = $this->getRepository('SharimgContentBundle:Content')->getAll($first, $maxResults);
        
        return array('pagination' => $contents);
    }
}
