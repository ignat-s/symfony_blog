<?php

namespace Acme\BlogBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Acme\BlogBundle\Document\Post;

interface PostRepositoryInterface extends ObjectRepository
{
    /**
     * @param string $permalink
     * @return Post|null
     */
    public function findOneByPermalink($permalink);
}
