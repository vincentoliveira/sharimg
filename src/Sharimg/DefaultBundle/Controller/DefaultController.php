<?php

namespace Sharimg\DefaultBundle\Controller;

use Sharimg\DefaultBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * CommonBundle DefaultController
 */
class DefaultController extends BaseController
{    
    /**
     * Homepage
     * 
     * @Template
     * @return Response
     */
    public function indexAction()
    {
        return $this->redirectToRoute('sharimg_content_homepage');
    }
    
    /**
     * Hello world test page
     * 
     * @Template
     * @return Response
     */
    public function helloworldAction()
    {
        return array();
    }
}
