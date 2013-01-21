<?php

namespace Acme\BlogBundle\Model;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Acme\BlogBundle\Document\User;

class UserManager implements UserProviderInterface
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var string
     */
    private $class;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    public function __construct(
        ObjectRepository $repository,
        EncoderFactoryInterface $encoderFactory,
        $class = 'Acme\BlogBundle\Document\User'
    ) {
        $this->repository = $repository;
        $this->encoderFactory = $encoderFactory;
        $this->class = $class;
    }

    public function createUser()
    {
        /** @var User $user */
        $user = new $this->class();
        $user->addRole(User::ROLE_USER);
        return $user;
    }

    public function updatePassword(User $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $encoder = $this->encoderFactory->getEncoder($user);

            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
            $user->eraseCredentials();
        }
    }

    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    public function findUserByEmail($email)
    {
        return $this->repository->findOneBy(array('email' => $email));
    }

    public function findUserByUsername($username)
    {
        return $this->repository->findOneBy(array('username' => $username));
    }


    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    public function loadUserByUsername($usernameOrEmail)
    {
        $user = $this->findUserByUsernameOrEmail($usernameOrEmail);

        if (!$user) {
            throw new UsernameNotFoundException(
                sprintf('No user "%s" was found.', $usernameOrEmail)
            );
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = $this->class;

        if (!$user instanceof $class) {
            throw new UnsupportedUserException('Account is not support.');
        }

        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Expected an instance of Acme\BlogBundle\Document\User, but got %s.', get_class($user))
            );
        }

        $refreshedUser = $this->findUserBy(array('id' => $user->getId()));
        if (null === $refreshedUser) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
        }

        return $refreshedUser;
    }

    public function supportsClass($class)
    {
        return $class === $this->class;
    }
}
