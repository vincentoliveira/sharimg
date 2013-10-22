<?php

namespace Sharimg\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SharimgDefaultBundle:Default:index.html.twig');
    }
}
