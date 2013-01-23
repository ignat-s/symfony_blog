<?php

namespace Acme\BlogBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Acme\BlogBundle\Model\User as AbstractUser;

/**
 * @MongoDB\Document(collection="users", repositoryClass="Acme\BlogBundle\Repository\MongoDB\UserRepository")
 */
class User extends AbstractUser
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $username;

    /**
     * @MongoDB\String
     */
    protected $email;

    /**
     * @MongoDB\String
     */
    protected $password;

    /**
     * @MongoDB\String
     */
    protected $salt;

    /**
     * @MongoDB\Collection
     */
    protected $roles;
}
