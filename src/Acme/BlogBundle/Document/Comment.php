<?php

namespace Acme\BlogBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Acme\BlogBundle\Model\Comment as AbstractComment;

/**
 * @MongoDB\EmbeddedDocument
 */
class Comment extends AbstractComment
{
    /**
     * @MongoDB\String
     */
    protected $body;

    /**
     * @MongoDB\String
     */
    protected $email;

    /**
     * @MongoDB\String
     */
    protected $author;

    /**
     * @MongoDB\Date
     */
    protected $createdAt;
}
