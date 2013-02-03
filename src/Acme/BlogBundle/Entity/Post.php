<?php

namespace Acme\BlogBundle\Entity;

use Acme\BlogBundle\Model\Comment as AbstractComment;
use Doctrine\ORM\Mapping as ORM;
use Acme\BlogBundle\Model\Post as AbstractPost;

/**
 * @ORM\Entity(repositoryClass="Acme\BlogBundle\Repository\ORM\PostRepository")
 * @ORM\Table(
 *      name="posts",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="permalink_idx", columns={"permalink"})
 *      },
 *      indexes={
 *          @ORM\Index(name="publication_date_idx", columns={"publicationDate"})
 *      }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class Post extends AbstractPost
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $body;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $permalink;

    /**
     * @ORM\Column(name="tags", type="string", length=1024)
     */
    protected $tagsAsString = array();

    /**
     * @ORM\OneToMany(targetEntity="Acme\BlogBundle\Entity\Comment", mappedBy="post")
     */
    protected $comments = array();

    /**
     * @ORM\Column(type="datetime")
     */
    protected $publicationDate;

    /**
     * @ORM\ManyToOne(targetEntity="Acme\BlogBundle\Entity\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $author;

    public function addComment(AbstractComment $comment)
    {
        parent::addComment($comment);
        $comment->setPost($this);
    }

    /**
     * @ORM\PostLoad
     */
    public function restoreTags()
    {
        $this->tags = explode(', ', $this->tagsAsString);
    }

    /**
     * @ORM\PrePersist
     */
    public function saveTags()
    {
        $this->tagsAsString = implode(', ', $this->tags);
    }
}
