<?php

namespace Acme\BlogBundle\Repository;

use Acme\BlogBundle\Document\User;
use Doctrine\Common\Persistence\ObjectRepository;

interface UserRepositoryInterface extends ObjectRepository
{
    /**
     * Find user by email
     *
     * @param string $email
     * @return User
     */
    public function findOneByEmail($email);

    /**
     * Find user by username
     *
     * @param string $username
     * @return User
     */
    public function findOneByUsername($username);

    /**
     * Find user by username or email
     *
     * @param string $usernameOrEmail
     * @return User
     */
    public function findOneByUsernameOrEmail($usernameOrEmail);
}
