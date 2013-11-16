<?php

namespace Sharimg\ContentBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Sharimg\DefaultBundle\Controller\ApiController;
use Sharimg\UserBundle\Entity\User;
use Sharimg\ContentBundle\Entity\Favorite;

/**
 * FavoriteService
 */
class FavoriteService
{
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
    
    /**
     * Get favorite list of $user
     * @return array 
     */
    public function getList(User $user)
    {
        $page = $this->container->get('request')->query->get('page');
        if (!isset($page)) {
            $page = 1;
        }
        $maxResults = $this->container->getParameter('sharimg.content.pagination');
        $firstResult = ($page - 1) * $maxResults;
        
        $repo = $this->em->getRepository('SharimgContentBundle:Favorite');
        $count = $repo->createQueryBuilder('favorite')
                ->select('count(favorite)')
                ->where('favorite.user = :user')
                ->setParameter(':user', $user)
                ->getQuery()
                ->getSingleScalarResult()
        ;
        $contents = $repo->createQueryBuilder('favorite')
                ->select('favorite, content, media')
                ->leftJoin('favorite.content', 'content')
                ->leftJoin('content.media', 'media')
                ->where('favorite.user = :user')
                ->setParameter(':user', $user)
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResults)
                ->getQuery()
                ->getArrayResult()
        ;
        
        return array(
            'page' => $page,
            'count' => $count,
            'favorites' => $contents,
        );
    }
}
