<?php

namespace Acme\BlogBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

abstract class Post
{
    /**
     * @var string|int
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    protected $title;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $permalink;

    /**
     * @var array
     */
    protected $tags = array();

    /**
     * @var array
     */
    protected $comments = array();

    /**
     * @Assert\Type("DateTime")
     * @var \DateTime
     */
    protected $publicationDate;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("Acme\BlogBundle\Model\User")
     */
    protected $author;

    public function __construct()
    {
        $this->publicationDate = new \DateTime();
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $permalink
     */
    public function setPermalink($permalink)
    {
        $this->permalink = $permalink;
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        return $this->permalink;
    }

    /**
     * @return string
     */
    public function getTagsString()
    {
        return implode(', ', $this->tags);
    }

    /**
     * @param string $tagsString
     */
    public function setTagsString($tagsString)
    {
        $this->setTags(explode(', ', $tagsString));
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags)
    {
        $this->tags = array();
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }
    }

    /**
     * @param string $tag
     */
    public function addTag($tag)
    {
        if ($tag && !in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
    }

    /**
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setPublicationDate(\DateTime $dateTime)
    {
        $this->publicationDate = $dateTime;
    }

    /**
     * @return \DateTime
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Check users are equals
     *
     * @param mixed $object
     * @return bool
     */
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
