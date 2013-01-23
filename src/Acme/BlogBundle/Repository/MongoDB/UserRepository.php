<?php

namespace Acme\BlogBundle\Repository\MongoDB;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Acme\BlogBundle\Repository\UserRepositoryInterface;

class UserRepository extends DocumentRepository implements UserRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function findOneByEmail($email)
    {
        return $this->findOneBy(array('email' => $email));
    }

    /**
     * {@inheritDoc}
     */
    public function findOneByUsername($username)
    {
        return $this->findOneBy(array('username' => $username));
    }

    /**
     * {@inheritDoc}
     */
    public function findOneByUsernameOrEmail($usernameOrEmail)
    {
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->findOneByEmail($usernameOrEmail);
        }

        return $this->findOneByUsername($usernameOrEmail);
    }
}
