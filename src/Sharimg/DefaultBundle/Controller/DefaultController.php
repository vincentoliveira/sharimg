<?php

namespace Sharimg\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * CommonBundle DefaultController
 */
class DefaultController extends Controller
{
    /**
     * Homepage
     * 
     * @Template
     * @return Response
     */
    public function indexAction()
    {
        return array();
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
