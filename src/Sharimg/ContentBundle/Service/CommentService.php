<?php

namespace Sharimg\ContentBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Sharimg\ContentBundle\Entity\Content;
use Sharimg\DefaultBundle\Controller\ApiController;

use Sharimg\ContentBundle\Entity\Comment;

/**
 * CommentService
 */
class CommentService
{
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
    
    /**
     * Get comment list of content
     * 
     * @param int $contentId Post parameters
     * @return array list of comments
     */
    public function getList($contentId = null)
    {
        $repo = $this->em->getRepository('SharimgContentBundle:Comment');
        
        $request = $this->container->get('request');
        
        if ($contentId === null) {
            $contentId = $request->query->get('content_id');
        }
        
        $page = $request->query->get('page');
        if ($page === null || $page < 1) {
            $page = 1;
        }
        $count = $this->container->getParameter('sharimg.comment.pagination');
        $firstResult = ($page - 1) * $count;
        
        $results = $repo->getComments($contentId, $count, $firstResult);
        foreach ($results as &$result) {
            $userInfo = $result['user'];
            $user = array(
                'id' => $userInfo['id'],
                'username' => $userInfo['username'],
                'email' => $userInfo['email'],
            );
            unset($result['user']);
            $result['user'] = $user;
        }
        
        return $results;
    }
    
    /**
     * Get comment list of content
     * 
     * @param int $userId 
     * @param int $contentId Post parameters
     * @return array list of comments
     */
    public function post($userId, $contentId = null)
    {
        $request = $this->container->get('request');
        
        $text = $request->request->get('comment');
        if ($text === null || $text === '') {
            return false;
        }
        
        // user
        $token = $this->container->get('security.context')->getToken();
        $user = $token !== null ? $token->getUser() : null;
        if (!$user instanceof \Sharimg\UserBundle\Entity\User) {
            return false;
        }
        
        if ($contentId === null) {
            $contentId = $request->query->get('content_id');
        }
        
        $content = $this->em->getRepository('SharimgContentBundle:Content')->find($contentId);
        if ($content === null) {
            return false;
        }
        
        $comment = new Comment();
        $comment->setText($text);
        $comment->setUser($user);
        $comment->setContent($content);
        $comment->setStatusId(1);
        
        $this->em->persist($comment);
        $this->em->flush();
        
        return true;        
    }
}
