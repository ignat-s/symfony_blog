<?php

namespace Acme\BlogBundle\Repository\MongoDB;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Acme\BlogBundle\Repository\PostRepositoryInterface;
use Acme\BlogBundle\Document\Post;

class PostRepository extends DocumentRepository implements PostRepositoryInterface
{
    /**
     * @param string $permalink
     * @return Post|null
     */
    public function findOneByPermalink($permalink)
    {
        return $this->findOneBy(array('permalink' => new \MongoRegex('/^' . preg_quote($permalink) . '$/i')));
    }
}
