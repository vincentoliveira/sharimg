<?php

namespace Sharimg\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sharimg\ContentBundle\Entity\Media;

/**
 * Content
 *
 * @ORM\Table(name="content")
 * @ORM\Entity(repositoryClass="Sharimg\ContentBundle\Repository\ContentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Content
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
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var Media
     *
     * @ORM\OneToOne(targetEntity="Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     */
    private $media;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Sharimg\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="contributor_id", referencedColumnName="id")
     */
    private $contributor;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=31, nullable=true)
     */
    private $source;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="status_id", type="integer", length=2)
     */
    private $statusId;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="favorite_count", type="integer", nullable=true)
     */
    private $favoriteCount;

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
     * @return Content
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
     * Set description
     *
     * @param string $description
     * @return Content
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set source
     *
     * @param string $source
     * @return Content
     */
    public function setSource($source)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set media
     *
     * @param Media $media
     * @return Content
     */
    public function setMedia(Media $media = null)
    {
        $this->media = $media;
    
        return $this;
    }

    /**
     * Get media
     *
     * @return Media 
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set statusId
     *
     * @param boolean $statusId
     * @return Content
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
    
        return $this;
    }

    /**
     * Get statusId
     *
     * @return boolean 
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * Set contributor
     *
     * @param \Sharimg\UserBundle\Entity\User $contributor
     * @return Content
     */
    public function setContributor(\Sharimg\UserBundle\Entity\User $contributor = null)
    {
        $this->contributor = $contributor;
    
        return $this;
    }

    /**
     * Get contributor
     *
     * @return \Sharimg\UserBundle\Entity\User 
     */
    public function getContributor()
    {
        return $this->contributor;
    }

    /**
     * Set favoriteCount
     *
     * @param integer $favoriteCount
     * @return Content
     */
    public function setFavoriteCount($favoriteCount)
    {
        $this->favoriteCount = $favoriteCount;
    
        return $this;
    }

    /**
     * Get favoriteCount
     *
     * @return integer 
     */
    public function getFavoriteCount()
    {
        return $this->favoriteCount;
    }
}