<?php

namespace Acme\BlogBundle\Model;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Acme\BlogBundle\Repository\UserRepositoryInterface;
use Acme\BlogBundle\Model\User;

class UserManager implements UserProviderInterface
{
    /**
     * @var DomainFactoryInterface
     */
    private $domainFactory;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @param DomainFactoryInterface $domainFactory
     * @param UserRepositoryInterface $userRepository
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        DomainFactoryInterface $domainFactory,
        UserRepositoryInterface $userRepository,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->domainFactory = $domainFactory;
        $this->userRepository = $userRepository;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * Creates user with ROLE_USER
     *
     * @return User
     */
    public function createUser()
    {
        $user = $this->domainFactory->createUser();
        $user->addRole(User::ROLE_USER);
        return $user;
    }

    /**
     * Updates user with encored password if plain password is set.
     *
     * @param User $user
     */
    public function updatePassword(User $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $encoder = $this->encoderFactory->getEncoder($user);

            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
            $user->eraseCredentials();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($usernameOrEmail)
    {
        $user = $this->userRepository->findOneByUsernameOrEmail($usernameOrEmail);

        if (!$user) {
            throw new UsernameNotFoundException(
                sprintf('No user "%s" was found.', $usernameOrEmail)
            );
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Account %s is not support.', get_class($user))
            );
        }

        $refreshedUser = $this->userRepository->findOneBy(array('id' => $user->getId()));
        if (null === $refreshedUser) {
            throw new UsernameNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $refreshedUser;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return class_exists($class) && in_array('Acme\BlogBundle\Model\User', class_parents($class));
    }
}
