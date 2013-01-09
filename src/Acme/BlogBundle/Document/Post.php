<?php

namespace Acme\BlogBundle\Document;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;

/**
 * @MongoDB\Document(repositoryClass="Acme\BlogBundle\Repository\MongoDB\PostRepository")
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
