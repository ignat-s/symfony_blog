<?php

namespace Acme\BlogBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Acme\BlogBundle\Model\Post;

interface PostRepositoryInterface extends ObjectRepository
{
    /**
     * Find post by permalink
     *
     * @param string $permalink
     * @return Post|null
     */
    public function findOneByPermalink($permalink);

    /**
     * Create query to retrieve posts ordered by publication date
     *
     * @return object Query
     */
    public function createOrderedPostsQuery();

    /**
     * Create query to retrieve posts ordered by publication date filtered by tag
     *
     * @param string $tag
     * @return object Query
     */
    public function createOrderedPostsWithTagQuery($tag);
}
