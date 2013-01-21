<?php

namespace Acme\BlogBundle\Document;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;

/**
 * @MongoDB\Document(collection="posts", repositoryClass="Acme\BlogBundle\Repository\MongoDB\PostRepository")
 * @MongoDBUnique(fields="permalink")
 */
class Post
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $body;

    /**
     * @MongoDB\String
     */
    private $permalink;

    /**
     * @MongoDB\Collection
     */
    private $tags = array();

    /**
     * @MongoDB\EmbedMany(targetDocument="Acme\BlogBundle\Document\Comment")
     */
    private $comments = array();

    /**
     * @MongoDB\Date
     * @MongoDB\Index
     * @Assert\Type("DateTime")
     */
    private $publicationDate;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Acme\BlogBundle\Document\User")
     * @Assert\NotBlank()
     */
    private $author;

    public function __construct()
    {
        $this->publicationDate = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setPermalink($permalink)
    {
        $this->permalink = $permalink;
    }

    public function getPermalink()
    {
        return $this->permalink;
    }

    public function getTagsString()
    {
        return implode(', ', $this->tags);
    }

    public function setTagsString($tagsString)
    {
        $this->setTags(explode(', ', $tagsString));
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags(array $tags)
    {
        $this->tags = array();
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }
    }

    public function addTag($tag)
    {
        if ($tag && !in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
    }

    public function setPublicationDate(\DateTime $dateTime)
    {
        $this->publicationDate = $dateTime;
    }

    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    public function setAuthor(User $author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function equalsTo($object)
    {
        if ($object instanceof self) {
            if ($this->id != $object->id) {
                return false;
            }
            return $this === $object;
        }
        return false;
    }
}
