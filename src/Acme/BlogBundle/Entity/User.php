<?php

namespace Acme\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Acme\BlogBundle\Model\User as AbstractUser;

/**
 * @ORM\Entity(repositoryClass="Acme\BlogBundle\Repository\ORM\UserRepository")
 * @ORM\Table(
 *      name="users",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="username_idx", columns={"username"}),
 *          @ORM\UniqueConstraint(name="email_idx", columns={"email"}),
 *      },
 *      indexes={
 *          @ORM\Index(name="search_idx", columns={"username", "email"})
 *      }
 * )
 */
class User extends AbstractUser
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
    protected $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $salt;

    /**
     * @ORM\Column(type="array")
     */
    protected $roles;
}
