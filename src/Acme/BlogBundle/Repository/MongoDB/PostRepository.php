<?php

namespace Acme\BlogBundle\Repository\MongoDB;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Acme\BlogBundle\Repository\PostRepositoryInterface;
use Acme\BlogBundle\Document\Post;

class PostRepository extends DocumentRepository implements PostRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function findOneByPermalink($permalink)
    {
        return $this->findOneBy(array('permalink' => new \MongoRegex('/^' . preg_quote($permalink) . '$/i')));
    }

    /**
     * {@inheritDoc}
     */
    public function createOrderedPostsQuery()
    {
        return $this->createQueryBuilder()
            ->sort('publicationDate', 'desc')
            ->getQuery();
    }

    /**
     * {@inheritDoc}
     */
    public function createOrderedPostsWithTagQuery($tag)
    {
        return $this->createQueryBuilder()
            ->field('tags')->equals($tag)
            ->sort('publicationDate', 'desc')
            ->getQuery();
    }
}
