<?php
namespace Acme\BlogBundle\DataFixtures;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\BlogBundle\Model\PostManager;
use Acme\BlogBundle\Model\Post;
use Acme\BlogBundle\Model\User;

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
        $post = $this->createPost(
            'Unit testing',
            'The goal of unit testing is to isolate each part '
            . 'of the program and show that the individual parts are correct.',
            array('TDD', 'unit testing'),
            $this->getReference('user')
        );
        $manager->persist($post);

        $post = $this->createPost(
            'Functional testing',
            'Functional testing is a type of black box testing'
            . ' that bases its test cases on the specifications of the software component under test.',
            array('TDD', 'functional testing'),
            $this->getReference('user')
        );
        $manager->persist($post);

        $manager->flush();
    }

    /**
     * @param string $title
     * @param string $body
     * @param array $tags
     * @param User $author
     * @return Post
     */
    private function createPost($title, $body, array $tags, User $author)
    {
        $post = $this->postManager->createPost($author);
        $post->setTitle($title);
        $post->setBody($body);
        $post->setTags($tags);
        $this->postManager->updatePermalink($post);
        return $post;
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
