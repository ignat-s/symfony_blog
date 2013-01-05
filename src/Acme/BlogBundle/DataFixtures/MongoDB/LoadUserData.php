<?php
namespace Acme\HelloBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\BlogBundle\Document\User;
use Acme\BlogBundle\Model\UserManager;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
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
        $userAdmin = $this->userManager->createUser();
        $userAdmin->setUsername('admin');
        $userAdmin->setEmail('admin@example.com');
        $userAdmin->addRole(User::ROLE_ADMIN);
        $userAdmin->setPlainPassword('adminpass');
        $this->userManager->updatePassword($userAdmin);

        $manager->persist($userAdmin);
        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->userManager = $this->container->get('acme_blog.user_manager');
    }
}
