<?php

namespace Sharimg\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User entity (extends fosuser)
 * 
 * @ORM\Entity(repositoryClass="Sharimg\UserBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @var integer $id
     * @access protected
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}
