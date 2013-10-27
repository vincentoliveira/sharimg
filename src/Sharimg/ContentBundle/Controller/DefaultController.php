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
    
    
    /**
     * Homepage
     * 
     * @Template
     * @return Response
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $postParams = $request->request->all();
        $errors = array();
        
        if ($request->getMethod() == 'POST') {
            $formHandler = $this->container->get('sharimg_content.content_form_handler');
            $isValid = $formHandler->isValid($postParams);
            if ($isValid === true) {
                $contentId = $formHandler->hydrateEntity($postParams);
                if ($contentId !== false) {
                    // redirect to content
                }
                $errors['globals'][] = 'content.error.internal_error';
            } else {
                $errors = $isValid;
            }
        }
        
        return array(
            'input_data' => $postParams,
            'errors' => $errors,
        );
    }
}
