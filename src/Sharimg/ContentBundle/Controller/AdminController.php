<?php

namespace Sharimg\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sharimg\ContentBundle\Form\ContentType;

/**
 * AdminController
 */
class AdminController extends Controller
{
    /**
     * Add content action
     * 
     * @Template
     * @return Response
     */
    public function addAction()
    {
        $form = $this->createForm(new ContentType());
        
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                try {
                    $manager = $this->container->get('sharimg_content.content_manager');
                    $content = $manager->add($form->getData());
                    $this->container->get('session')->getFlashBag()->add(
                        'success',
                        'New content has been added.'
                    );
                }
                catch (\Exception $e) {
                    $this->container->get('session')->getFlashBag()->add(
                        'error',
                        'An error has occured: '.$e->getMessage()
                    );
                }
            }
        }
        
        return array(
            'form' => $form,
        );
    }
}
