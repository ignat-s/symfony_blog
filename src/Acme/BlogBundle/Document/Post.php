<?php

namespace Acme\BlogBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Acme\BlogBundle\Model\Post as AbstractPost;

/**
 * @MongoDB\Document(collection="posts", repositoryClass="Acme\BlogBundle\Repository\MongoDB\PostRepository")
 * @MongoDBUnique(fields="permalink")
 */
class Post extends AbstractPost
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $title;

    /**
     * @MongoDB\String
     */
    protected $body;

    /**
     * @MongoDB\String
     */
    protected $permalink;

    /**
     * @MongoDB\Collection
     */
    protected $tags = array();

    /**
     * @MongoDB\EmbedMany(targetDocument="Acme\BlogBundle\Document\Comment")
     */
    protected $comments = array();

    /**
     * @MongoDB\Date
     * @MongoDB\Index
     */
    protected $publicationDate;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Acme\BlogBundle\Document\User")
     */
    protected $author;
}
