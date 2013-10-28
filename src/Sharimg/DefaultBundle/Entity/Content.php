<?php

namespace Sharimg\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sharimg\DefaultBundle\Helper\CommonHelper;

/**
 * Content
 *
 * @ORM\Table(name="content")
 * @ORM\Entity(repositoryClass="Sharimg\DefaultBundle\Repository\ContentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Content
{
    const THUMB_WIDTH = 480;
    
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
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=127, nullable=false)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="from_", type="string", length=31, nullable=true)
     */
    private $from;
    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;
    
    private $tmpPath;
    private $tmpFile;

    public function setTmpPath($tmpPath)
    {
        $this->tmpPath = $tmpPath;
        return $this;
    }
    public function setTmpFile($tmpFile)
    {
        $this->tmpFile = $tmpFile;
        return $this;
    }


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
     * Set path
     *
     * @param string $path
     * @return Content
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Content
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set from
     *
     * @param string $from
     * @return Content
     */
    public function setFrom($from)
    {
        $this->from = $from;
    
        return $this;
    }

    /**
     * Get from
     *
     * @return string 
     */
    public function getFrom()
    {
        return $this->from;
    }
    
    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }
    
     public function getThumbAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/min/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    public function getThumbWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/min/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'images';
    }
    
        /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->tmpPath) {
            $ext = basename(mime_content_type($this->tmpPath));
            $this->path = sha1(uniqid(mt_rand(), true)).'.'.$ext;
        }
        if (null !== $this->tmpFile) {
            $this->path = sha1(uniqid(mt_rand(), true)).'.'.$this->tmpFile->guessExtension();
            $this->tmpPath = $this->tmpFile->getPathName();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->tmpFile && null === $this->tmpPath) {
            return;
        }
        
        $path = $this->getUploadRootDir() . '/' . $this->path;
        copy($this->tmpPath, $path);
        
        // generate thumbnail
        $thumbnailPath = $this->getUploadRootDir() . '/min/' . $this->path;
        CommonHelper::resizeImage($this->getAbsolutePath(), $thumbnailPath, self::THUMB_WIDTH);

        unset($this->tmpFile);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
        if ($file = $this->getThumbAbsolutePath()) {
            unlink($file);
        }
    }
}