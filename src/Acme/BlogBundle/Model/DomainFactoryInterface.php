<?php

namespace Acme\BlogBundle\Model;

interface DomainFactoryInterface
{
    /**
     * Create user
     *
     * @return User
     */
    public function createUser();

    /**
     * Create post
     *
     * @return Post
     */
    public function createPost();

    /**
     * Create comment
     *
     * @return Comment
     */
    public function createComment();
}
