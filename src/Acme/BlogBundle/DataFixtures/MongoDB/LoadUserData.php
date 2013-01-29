<?php
namespace Acme\BlogBundle\DataFixtures\MongoDB;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\BlogBundle\Model\User;
use Acme\BlogBundle\Model\UserManager;

class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $admin = $this->createUser('admin', 'adminpass', 'admin@example.com', array(User::ROLE_ADMIN));
        $manager->persist($admin);
        $this->addReference('admin', $admin);

        $user = $this->createUser('user', 'userpass', 'user@example.com', array(User::ROLE_USER));
        $manager->persist($user);
        $this->addReference('user', $user);

        $manager->flush();
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @param array $roles
     * @return User
     */
    private function createUser($username, $password, $email, array $roles)
    {
        $user = $this->userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPlainPassword($password);
        $this->userManager->updatePassword($user);

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->userManager = $this->container->get('acme_blog.user_manager');
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
