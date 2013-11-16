<?php

namespace Sharimg\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sharimg\ContentBundle\Entity\Media;

/**
 * Content
 *
 * @ORM\Table(name="favorite")
 * @ORM\Entity(repositoryClass="Sharimg\ContentBundle\Repository\ContentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Favorite
{    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Sharimg\ContentBundle\Entity\Content")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Sharimg\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Favorite
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set content
     *
     * @param \Sharimg\ContentBundle\Entity\Content $content
     * @return Favorite
     */
    public function setContent(\Sharimg\ContentBundle\Entity\Content $content = null)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return \Sharimg\ContentBundle\Entity\Content 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set user
     *
     * @param \Sharimg\UserBundle\Entity\User $user
     * @return Favorite
     */
    public function setUser(\Sharimg\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Sharimg\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}