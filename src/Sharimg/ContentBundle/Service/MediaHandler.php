<?php

namespace Sharimg\ContentBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

use Sharimg\ContentBundle\Entity\Media;

/**
 * MediaHandler: Handle media
 */
class MediaHandler
{
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
    
    /**
     * Create media from file
     * @param type $file
     * @throws \InvalidArgumentException
     * @throws InternalErrorException
     * @return Media
     */
    public function createMediaFromFile($file)
    {
        if ($file === null) {
            throw new \InvalidArgumentException("Empty file input");
        }
        
        $path = $id = $this->generateUID();
        if ($id === null) {
            throw new InternalErrorException("Failed to generate media UID");
        }
        $ext = $file->guessExtension();
        if ($ext !== null) {
            $path = $path . '.' . $ext;
        }
        
        $this->copyMedia($file->getPathName(), $path);
        
        $media = new Media();
        $media->setId($id);
        $media->setPath($path);
        
        $this->em->persist($media);
        $this->em->flush();
        
        return $media;
    }
    
    /**
     * Create media from url
     * @param string $mediaUrl
     * @throws \InvalidArgumentException
     * @throws InternalErrorException
     * @return Media
     */
    public function createMediaFromUrl($mediaUrl)
    {   
        $tmpPath = $this->getTmpDownloadDir() . sha1(uniqid(mt_rand(), true));
        
        if ($mediaUrl === null || !$this->downloadMedia($mediaUrl, $tmpPath)) {
            throw new \InvalidArgumentException("Invalid media url");
        }
        
        $path = $id = $this->generateUID();
        if ($id === null) {
            throw new InternalErrorException("Failed to generate media UID");
        }
        $ext = basename(mime_content_type($tmpPath));
        if ($ext !== null) {
            $path = $path . '.' . $ext;
        }
        
        $this->copyMedia($tmpPath, $path);
        
        $media = new Media();
        $media->setId($id);
        $media->setPath($path);
        
        $this->em->persist($media);
        $this->em->flush();
        
        return $media;
    }
    
    /**
     * Generate media unique ID
     * @return string UID
     */
    protected function generateUID()
    {
        $repo = $this->em->getRepository('SharimgContentBundle:Media');
        for ($i=1; $i<=100; $i++) {
            $id = $this->generateId();
            if ($repo->find($id) === null) {
                return $id;
            }
        }
        
        return null;
    }
    
    /**
     * Generate media unique ID
     * @return string UID
     */
    protected function generateID()
    {
        $key = '';
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        for($i=0; $i<12; $i++) $key .= $charset[(mt_rand(0,(strlen($charset)-1)))]; 

        return $key;
    }
    
    /**
     * Download $mediaUrl to $path
     * @param string $mediaUrl
     * @param string $dir
     * @return boolean
     */
    protected function downloadMedia($mediaUrl, $path = '/tmp/download')
    {
        $ch = curl_init($mediaUrl);
        $fp = fopen($path, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        fclose($fp);
        
        return $httpStatus == 200;
    }
    
    /**
     * Copy $srcPath into media directory with $dstFilename as filename
     * Generate a thumbnail in thumbnail media directory
     * 
     * @param string $srcPath
     * @param string $dstFilename
     */
    protected function copyMedia($srcPath, $dstFilename)
    {
        $thumbWidth = $this->container->getParameter('media.thumbnail.width');
        
        $dstPath = $this->getUploadDir() . $dstFilename;
        copy($srcPath, $dstPath);
        
        // generate thumbnail
        $thumbnailPath = $this->getThumbnailUploadDir() . $dstFilename;
        $this->resizeImage($dstPath, $thumbnailPath, $thumbWidth);

//        unset($srcPath);
    }
    
    /**
     * Get temporary download directory path
     * @return string temporary download directory path
     */
    protected function getTmpDownloadDir()
    {
        $root = __DIR__ . '/../../../../web/';
        $downloadDir = $this->container->getParameter('media.tmp_download_dir');        
        return (substr($downloadDir, 0, 1) === '/' ? '' : $root) . $downloadDir . (substr($downloadDir, -1) === '/' ? '' : '/');
    }
    
    /**
     * Get upload directory path
     * @return string upload directory path
     */
    protected function getUploadDir()
    {
        $root = __DIR__ . '/../../../../web/';
        $uploadDir = $this->container->getParameter('media.upload_dir');        
        return (substr($uploadDir, 0, 1) === '/' ? '' : $root) . $uploadDir . (substr($uploadDir, -1) === '/' ? '' : '/');
    }
    
    /**
     * Get thumbnail upload directory path
     * @return string thumbnailupload directory path
     */
    protected function getThumbnailUploadDir()
    {
        $root = __DIR__ . '/../../../../web/';
        $uploadDir = $this->container->getParameter('media.thumbnail.upload_dir');        
        return (substr($uploadDir, 0, 1) === '/' ? '' : $root) . $uploadDir . (substr($uploadDir, -1) === '/' ? '' : '/');
    }
    
    
    /**
     * Permet de créer les vignettes à chaque upload d'image
     * 
     * @param string $srcImagePath  Chemin d'accès à l'image originale (sur le serveur)
     * @param string $dstImagePath  Chemin de destination de l'image redimensionné (sur le serveur)
     * @param string $maxWidth      Largeur de l'image voulue
     * @param string $maxHeight     Hauteur de l'image voulue (facultatif)
     * @param bool   $getSizes      If true, just return sizes, else do the job
     * 
     * @return boolean
     */
    protected function resizeImage($srcImagePath, $dstImagePath, $maxWidth = null, $maxHeight = null, $getSizes = false)
    {
        if (empty($srcImagePath) || !file_exists($srcImagePath) || is_dir($srcImagePath) || empty($maxWidth)) {
            return false;
        }

        list($srcImageWidth, $srcImageHeight, $source_image_type) = getimagesize($srcImagePath);
        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                $srcGdImage = imagecreatefromgif($srcImagePath);
                break;
            case IMAGETYPE_JPEG:
                $srcGdImage = imagecreatefromjpeg($srcImagePath);
                break;
            case IMAGETYPE_PNG:
                $srcGdImage = imagecreatefrompng($srcImagePath);
                break;
        }
        if (!isset($srcGdImage) || $srcGdImage === false) {
            return false;
        }
        
        $thumbnailImageWith = $maxWidth;
        if ($maxHeight != null) {
            $thumbnailImageHeight = $maxHeight;
        }
        else {
            $thumbnailImageHeight = (int) ($srcImageHeight * $maxWidth / $srcImageWidth );
        }
        
        // if getSizes option, just return sizes
        if ($getSizes) {
            return array(
                'oldWidth' => $srcImageWidth,
                'oldHeight' => $srcImageHeight,
                'newWidth' => $thumbnailImageWith,
                'newHeight' => $thumbnailImageHeight,
            );
        }
        
        if ($thumbnailImageWith >= $srcImageWidth || $thumbnailImageHeight >= $srcImageHeight) {
            copy($srcImagePath, $dstImagePath);
            return;
        }
        
        $thumbnailGdImage = imagecreatetruecolor($thumbnailImageWith, $thumbnailImageHeight);
        imagealphablending($thumbnailGdImage, false);
        imagesavealpha($thumbnailGdImage, true);
        imagecopyresampled($thumbnailGdImage, $srcGdImage, 0, 0, 0, 0, $thumbnailImageWith, $thumbnailImageHeight, $srcImageWidth, $srcImageHeight);

        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                imagegif($thumbnailGdImage, $dstImagePath);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($thumbnailGdImage, $dstImagePath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbnailGdImage, $dstImagePath, 0);
                break;
        }
        imagedestroy($srcGdImage);
        imagedestroy($thumbnailGdImage);

        return true;
    }
}
