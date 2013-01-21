<?php

namespace Acme\BlogBundle\Document;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\EmbeddedDocument
 */
class Comment
{
    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $body;

    /**
     * @MongoDB\String
     * @Assert\Email()
     */
    private $email;

    /**
     * @MongoDB\String
     */
    private $author;

    /**
     * @MongoDB\Date
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
