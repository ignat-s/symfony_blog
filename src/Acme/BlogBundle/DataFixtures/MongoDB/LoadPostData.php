<?php
namespace Acme\HelloBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\BlogBundle\Model\PostManager;
use Acme\BlogBundle\Document\Post;
use Acme\BlogBundle\Document\Tag;

class LoadPostData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var PostManager
     */
    private $postManager;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $post = new Post();
        $post->setTitle('Unit testing');
        $post->setBody(
            'The goal of unit testing is to isolate each part '
            . 'of the program and show that the individual parts are correct.'
        );
        $post->addTag('TDD');
        $post->addTag('unit testing');

        $post->setAuthor($this->getReference('user'));

        $this->postManager->updatePermalink($post);

        $manager->persist($post);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->postManager = $this->container->get('acme_blog.post_manager');
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
