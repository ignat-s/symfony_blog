<?php

namespace Acme\BlogBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

abstract class User implements UserInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @var string|int
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    protected $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $salt;

    /**
     * @var array
     */
    protected $roles;

    /**
     * @Assert\NotBlank(groups={"registration"})
     * @var string
     */
    protected $plainPassword;

    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->roles = array();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = array();
        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function addRole($role)
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole($role)
    {
        if (false !== $key = array_search($role, $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    public function hasRole($role)
    {
        return in_array($role, $this->roles, true);
    }
}
