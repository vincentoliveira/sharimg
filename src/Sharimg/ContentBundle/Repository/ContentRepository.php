<?php

namespace Sharimg\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ContentRepository
 */
class ContentRepository extends EntityRepository
{
    /**
     * Get visible contents (paginated)
     * @param array $params
     * @return array
     */
    public function getVisibleList($params = array())
    {
        $query = $this->createQueryBuilder('content')
                ->select('content, media')
                ->leftJoin('content.media', 'media')
                ->where('content.statusId > 0')
                ->andWhere('content.date < :now')
                ->setParameter(':now', new \DateTime())
                ->orderBy('content.date', 'DESC')
                ->addOrderBy('content.id', 'DESC')
        ;
        
        if (isset($params['count'])) {
            $query->setMaxResults($params['count']);
        }
        if (isset($params['since_id'])) {
            $query->andWhere('content.id > :since_id')
                    ->setParameter(':since_id', $params['since_id']);
        }
        if (isset($params['max_id'])) {
            $query->andWhere('content.id < :max_id')
                    ->setParameter(':max_id', $params['max_id']);
        }
        
        return $query->getQuery()->getArrayResult();
    }
    
    /**
     * Get all contents (paginated)
     * @param $first
     * @param $maxResults
     * @return array ['count'] => maxJokes, ['contents'] => paginated contents
     */
    public function getAll($first = 0, $maxResults = 0)
    {
        $query = $this->createQueryBuilder('content');
        
        // count pictures
        $count = $query
                ->select('count(content)')
                ->getQuery()
                ->getSingleScalarResult()
        ;
        
        // get paginated contents
        $query->select('content')
                ->orderBy('content.date', 'DESC')
                ->addOrderBy('content.id', 'DESC')
        ;

        if ($first > 0) {
            $query->setFirstResult($first);
        }
        if ($maxResults > 0) {
            $query->setMaxResults($maxResults);
        }
        
        $contents = $query->getQuery()->getResult();
        
        return array(
            'count' => $count,
            'contents' => $contents,
        );
    }
    
    /**
     * Get a random visible content
     * @return content
     */
    public function getRandom()
    {
        $queryBuilder = $this->createQueryBuilder('content')
                ->where('content.statusId > 0')
        ;

        // count pictures
        $count = $queryBuilder
                ->select('count(content)')
                ->getQuery()
                ->getSingleScalarResult()
        ;

        $seed = mt_rand(0, $count - 1);
        $queryBuilder->select('content')
                ->setFirstResult($seed)
                ->setMaxResults(1)
        ;

        $content = $queryBuilder
                        ->getQuery()
                        ->getOneOrNullResult()
        ;
        
        if ($content == null) {
            return null;
        }
        
        return $content;
    }
    
    /**
     * Get content details
     * @param int $contentId
     * @return Content
     */
    public function getContentDetails($contentId)
    {
        $queryBuilder = $this->createQueryBuilder('content')
                ->select('content, media')
                ->leftJoin('content.media', 'media')
                ->where('content.id = :contentId')
                ->setParameter(':contentId', $contentId)
        ;
        
        return $queryBuilder->getQuery()->getArrayResult();
    }
}