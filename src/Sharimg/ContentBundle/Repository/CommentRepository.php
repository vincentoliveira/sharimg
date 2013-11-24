<?php

namespace Sharimg\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ContentRepository
 */
class CommentRepository extends EntityRepository
{
    /**
     * Get comments
     * 
     * @param int $contentId
     * @param int $count
     * @param int $firstResults
     * @return array
     */
    public function getComments($contentId, $count = 20, $firstResults = 0)
    {
        $query = $this->createQueryBuilder('comment')
                ->select('comment, user')
                ->leftJoin('comment.user', 'user')
                ->where('comment.content = :content')
                ->orderBy('comment.date', 'ASC')
                ->setParameter(':content', $contentId)
                ->setMaxResults($count)
                ->setFirstResult($firstResults)
        ;
        
        return $query->getQuery()->getArrayResult();
    }
    
}