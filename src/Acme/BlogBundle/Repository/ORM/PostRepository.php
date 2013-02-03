<?php

namespace Acme\BlogBundle\Repository\ORM;

use Doctrine\ORM\EntityRepository;
use Acme\BlogBundle\Repository\PostRepositoryInterface;

class PostRepository extends EntityRepository implements PostRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function findOneByPermalink($permalink)
    {
        return $this->createQueryBuilder('p')
            ->where('lower(p.permalink) = ?1')
            ->setParameter(1, mb_strtolower($permalink))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritDoc}
     */
    public function createOrderedPostsQuery()
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.publicationDate', 'desc')
            ->getQuery();
    }

    /**
     * {@inheritDoc}
     */
    public function createOrderedPostsWithTagQuery($tag)
    {
        return $this->createQueryBuilder('p')
            ->where('lower(p.tagsAsString) like ?1')
            ->setParameter(1, '%' . mb_strtolower($tag) . '%')
            ->orderBy('p.publicationDate', 'desc')
            ->getQuery();
    }
}
