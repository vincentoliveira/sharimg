<?php

namespace Sharimg\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use FOS\UserBundle\Model\UserInterface;


/**
 * UserBundle DefaultController
 */
class DefaultController extends Controller
{
    /**
     * Profile action
     * 
     * @Secure(roles="ROLE_USER")
     * @Template()
     * @return Response
     */
    public function profileAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        return array('user' => $user);
    }
}
