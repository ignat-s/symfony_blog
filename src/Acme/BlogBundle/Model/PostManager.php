<?php

namespace Acme\BlogBundle\Model;

use Acme\BlogBundle\Repository\PostRepositoryInterface;
use Acme\BlogBundle\Document\Post;

class PostManager
{
    /**
     * @var PostRepositoryInterface
     */
    private $repository;

    public function __construct(PostRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function updatePermalink(Post $post)
    {
        $post->setPermalink($this->generatePermalink($post));
    }

    private function generatePermalink(Post $post, $level = 0)
    {
        $permalink = $this->addPermalinkSuffix($post->getTitle(), $level);
        $permalink = $this->stripRestrictedPermalinkCharacters($permalink);

        if ($this->hasAnotherPostWithPermalink($post, $permalink)) {
            return $this->generatePermalink($post, $level + 1);
        }

        return $permalink;
    }

    private function stripRestrictedPermalinkCharacters($string)
    {
        $string = preg_replace('/[\s]/', '_', $string);
        $string = preg_replace('/[\W]/', '', $string);
        return $string;
    }

    private function addPermalinkSuffix($string, $level)
    {
        if ($level > 0) {
            $string .= ' ' . $level;
        }
        return $string;
    }

    private function hasAnotherPostWithPermalink(Post $post, $permalink)
    {
        $anotherPost = $this->repository->findOneByPermalink($permalink);
        return (bool)$anotherPost && !$post->equalsTo($anotherPost);
    }
}
