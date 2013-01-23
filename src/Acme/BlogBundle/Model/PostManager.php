<?php

namespace Acme\BlogBundle\Model;

use Acme\BlogBundle\Repository\PostRepositoryInterface;
use Acme\BlogBundle\Model\Post;

class PostManager
{
    /**
     * @var DomainFactoryInterface
     */
    private $domainFactory;

    /**
     * @var PostRepositoryInterface
     */
    private $repository;

    /**
     * @param DomainFactoryInterface $domainFactory
     * @param PostRepositoryInterface $repository
     */
    public function __construct(DomainFactoryInterface $domainFactory, PostRepositoryInterface $repository)
    {
        $this->domainFactory = $domainFactory;
        $this->repository = $repository;
    }

    /**
     * Creates post
     *
     * @param User $user
     * @return Post
     */
    public function createPost(User $user)
    {
        $post = $this->domainFactory->createPost();
        $post->setAuthor($user);
        return $post;
    }

    /**
     * Creates and adds new comment to post
     *
     * @param Post $post
     * @return Comment
     */
    public function addNewPostComment(Post $post)
    {
        $comment = $this->domainFactory->createComment();
        $post->addComment($comment);
        return $comment;
    }

    /**
     * Updates post permalink according to post title
     *
     * @param Post $post
     */
    public function updatePermalink(Post $post)
    {
        $post->setPermalink($this->generatePermalink($post));
    }

    /**
     * Generates permalink for post
     *
     * @param Post $post
     * @param int $level
     * @return mixed
     */
    private function generatePermalink(Post $post, $level = 0)
    {
        $permalink = $this->addPermalinkSuffix($post->getTitle(), $level);
        $permalink = $this->stripRestrictedPermalinkCharacters($permalink);

        if ($this->hasAnotherPostWithPermalink($post, $permalink)) {
            return $this->generatePermalink($post, $level + 1);
        }

        return $permalink;
    }

    /**
     * Strip restricted characters from permalink string
     *
     * @param string $string
     * @return mixed
     */
    private function stripRestrictedPermalinkCharacters($string)
    {
        $string = preg_replace('/[\s]/', '_', $string);
        $string = preg_replace('/[\W]/', '', $string);
        return $string;
    }

    /**
     * Adds suffix from level to permalink string
     *
     * @param string $string
     * @param int $level
     * @return string
     */
    private function addPermalinkSuffix($string, $level)
    {
        if ($level > 0) {
            $string .= ' ' . $level;
        }
        return $string;
    }

    /**
     * Check post with same permalink is exist
     *
     * @param Post $post
     * @param string $permalink
     * @return bool
     */
    private function hasAnotherPostWithPermalink(Post $post, $permalink)
    {
        $anotherPost = $this->repository->findOneByPermalink($permalink);
        return (bool)$anotherPost && !$post->equalsTo($anotherPost);
    }
}
