<?php

namespace Sharimg\ContentBundle\Controller;

use Sharimg\DefaultBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
        
        $data = $this->getRepository('SharimgContentBundle:Content')->getAll($first, $maxResults);
        $data['maxPage'] = ceil($data['count'] / $maxResults);
        $data['page'] = $page;
        
        return $data;
    }
}
