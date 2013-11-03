<?php

namespace Sharimg\UserBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Sharimg\UserBundle\Entity\User;

/**
 * UserService: Handle use actions
 */
class UserService
{
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
    
    /**
     * Create user from POST $params
     * @param array $params
     * @return array errors or connection if params are valid
     */
    public function register(Array $params)
    {
        $requiredParams = array('email' => true, 'username' => true, 'password' => true);
        
        $user = new User();
        foreach ($params as $key => $value) {
            if ($key == 'password') {
                $key = 'PlainPassword';
                $requiredParams['password'] = false;
            }
            
            if (method_exists($user, 'set'.$key)) {
                $user->{'set'.$key}($value);
                if (isset($requiredParams[$key])) {
                    $requiredParams[$key] = false;
                }
            }
        }
        
        $errors = array();
        // check missing parameters
        foreach ($requiredParams as $key => $missing) {
            if ($missing) {
                $errors['missing_params'][] = $key;
            }
        }
        if (!empty($errors)) {
            return array('errors' => $errors);
        }
        
        
        // check email format
        $regex = '#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
        if (!preg_match($regex, $params['email'])) {
            $errors['bad_parameters'][] = 'email';            
        }
         
        // check unique parameters
        $uniqueParams = array('email', 'username');
        $repo = $this->em->getRepository('SharimgUserBundle:User');
        foreach ($uniqueParams as $unique) {
            if ($repo->findOneBy(array($unique => $params[$unique])) !== null) {
                $errors['email_format'][] = $unique;
            }
        }
        if (!empty($errors)) {
            return array('errors' => $errors);
        }
        
        try {
            $this->em->persist($user);
            $this->em->flush();
        } catch (\Exception $e) {
            return array('errors' => 'internal');
        }

        return $this->connect($user);
    }
    
    /**
     * Create connection data for $user
     * @param \Sharimg\UserBundle\Entity\User $user
     * @return array User connection data
     */
    protected function connect(User $user) {
        return array('success' => true);
    }
}
