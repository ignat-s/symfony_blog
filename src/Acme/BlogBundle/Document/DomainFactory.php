<?php

namespace Acme\BlogBundle\Document;

use Acme\BlogBundle\Model\DomainFactoryInterface;

class DomainFactory implements DomainFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createUser()
    {
        return new User();
    }

    /**
     * {@inheritDoc}
     */
    public function createPost()
    {
        return new Post();
    }

    /**
     * {@inheritDoc}
     */
    public function createComment()
    {
        return new Comment();
    }
}
