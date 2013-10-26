<?php

namespace Sharimg\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * DefaultController
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
}
