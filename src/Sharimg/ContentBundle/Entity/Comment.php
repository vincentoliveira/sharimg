<?php

namespace Sharimg\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Content
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="Sharimg\ContentBundle\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Comment
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
     * @var integer
     *
     * @ORM\Column(name="text", type="string", length=255, nullable=false)
     */
    private $text;

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
     * @var boolean
     *
     * @ORM\Column(name="status_id", type="integer", length=2)
     */
    private $statusId;

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
     * Set text
     *
     * @param boolean $text
     * @return Comment
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return boolean 
     */
    public function getText()
    {
        return $this->text;
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
    
    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->date = new \DateTime();
    }

    /**
     * Set statusId
     *
     * @param integer $statusId
     * @return Comment
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
    
        return $this;
    }

    /**
     * Get statusId
     *
     * @return integer 
     */
    public function getStatusId()
    {
        return $this->statusId;
    }
}