<?php

namespace Sharimg\ContentBundle\Controller;

use Sharimg\DefaultBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sharimg\ContentBundle\Entity\Content;

/**
 * DefaultController
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
        $params = array(
            'count' => $this->container->getParameter('sharimg.content.pagination'),
        );
        $contents = $this->getRepository('SharimgContentBundle:Content')->getVisibleList($params);
        
        return array('contents' => $contents);
    }
    
    /**
     * View content details
     * 
     * @Template
     * @ParamConverter("content", class="SharimgContentBundle:Content")
     * @param Content $content
     * @return Response
     */
    public function showAction(Content $content)
    {
        $favorized = false;
        
        $currentUser = $this->getCurrentUser();
        if ($currentUser !== null) {
            $favorite = $this->getRepository('SharimgContentBundle:Favorite')->findOneBy(array(
                'user' => $currentUser,
                'content' => $content,
            ));
            
            $favorized = $favorite != null && $favorite->getFavorized();
        }
        
        return array(
            'content' => $content,
            'favorized' => $favorized,
        );
    }
    
    /**
     * View random content details
     * 
     * @Template("SharimgContentBundle:Default:view.html.twig")
     * @return Response
     */
    public function randomAction()
    {
        $content = $this->getRepository('SharimgContentBundle:Content')->getRandom();
        return $this->redirectToRoute('sharimg_content_show', array('content' => $content->getId()));
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
        $errors = array();
        $params = array();
        
        if ($request->getMethod() == 'POST') {
            $postParams = $request->request->all();
            $fileParams = $request->files->all();
            $params = array_merge($postParams, $fileParams);
        
            $formHandler = $this->container->get('sharimg_content.content_form_handler');
            $isValid = $formHandler->isValid($params);
            if ($isValid === true) {
                $contentId = $formHandler->hydrateEntity($params);
                if ($contentId !== false) {
                    return $this->redirectToRoute('sharimg_content_show', array('content' => $contentId));
                }
                $errors['globals'][] = 'content.error.internal_error';
            } else {
                $errors = $isValid;
            }
        }
        
        return array(
            'input_data' => $params,
            'errors' => $errors,
        );
    }
}
