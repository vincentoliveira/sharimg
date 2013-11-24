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
        if (empty($page)) {
            $page = 1;
        }
        $maxResults = $this->container->getParameter('sharimg.content.pagination');
        $firstResult = ($page - 1) * $maxResults;

        $repo = $this->em->getRepository('SharimgContentBundle:Favorite');
        $count = $repo->createQueryBuilder('favorite')
                ->select('count(favorite)')
                ->where('favorite.favorized = true')
                ->andWhere('favorite.user = :user')
                ->setParameter(':user', $user)
                ->getQuery()
                ->getSingleScalarResult()
        ;
        $contents = $repo->createQueryBuilder('favorite')
                ->select('favorite, content, media')
                ->leftJoin('favorite.content', 'content')
                ->leftJoin('content.media', 'media')
                ->where('favorite.favorized = true')
                ->andWhere('favorite.user = :user')
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

    /**
     * Change favorize status
     * @return boolean
     */
    public function favorize()
    {
        // contributor
        $token = $this->container->get('security.context')->getToken();
        $user = $token !== null ? $token->getUser() : null;
        if (!$user instanceof \Sharimg\UserBundle\Entity\User) {
            return false;
        }

        $request = $this->container->get('request');
        $content_id = $request->request->get('content_id');
        $favorized = $request->request->get('favorized');

        if (empty($content_id)) {
            return array(ApiController::ERROR_MISSING_PARAMS => array('content_id'));
        }

        $content = $this->em->getRepository('SharimgContentBundle:Content')->find($content_id);
        if ($content === null) {
            return array(ApiController::ERROR_BAD_ARG => array('content_id'));
        }

        $favorite = $this->em->getRepository('SharimgContentBundle:Favorite')
                ->findOneBy(array(
            'content' => $content,
            'user' => $user
        ));

        if ($favorite === null) {
            $favorite = new Favorite();
            $favorite->setUser($user)->setContent($content);
            
            $content->setFavoriteCount($content->getFavoriteCount() + 1);
        }

        $favorite->setFavorized($favorized);
        $this->em->persist($favorite);
        $this->em->flush();

        return true;
    }


}
