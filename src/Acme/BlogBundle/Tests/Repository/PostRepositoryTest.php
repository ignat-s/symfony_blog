<?php

namespace Acme\BlogBundle\Tests\Respository;

use Doctrine\Common\Persistence\ObjectManager;
use Acme\BlogBundle\Repository\PostRepositoryInterface;
use Acme\BlogBundle\Test\WebTestCase;

class PostRepositoryTest extends WebTestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var PostRepositoryInterface
     */
    private $repository;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->objectManager = static::$kernel->getContainer()
            ->get('acme_blog.object_manager');

        $this->repository = $this->objectManager->getRepository('AcmeBlogBundle:Post');
    }

    /**
     * @dataProvider findOneByPermalinkHasResultDataProvider
     */
    public function testFindOneByPermalinkHasResult($searchPermalink, $expectedTitle, $expectedPermalink)
    {
        $post = $this->repository->findOneByPermalink($searchPermalink);
        $this->assertNotNull($post);
        $this->assertInstanceOf('Acme\BlogBundle\Model\Post', $post);
        $this->assertEquals($expectedTitle, $post->getTitle());
        $this->assertEquals($expectedPermalink, $post->getPermalink());
    }

    public function findOneByPermalinkHasResultDataProvider()
    {
        return array(
            array('unit_testing', 'Unit testing', 'Unit_testing'),
            array('UNIT_TESTING', 'Unit testing', 'Unit_testing'),
        );
    }

    /**
     * @dataProvider findOneByPermalinkHasNotResultDataProvider
     */
    public function testFindOneByPermalinkHasNotResult($searchPermalink)
    {
        $post = $this->repository->findOneByPermalink($searchPermalink);
        $this->assertNull($post);
    }

    public function findOneByPermalinkHasNotResultDataProvider()
    {
        return array(
            array('Unit testing'),
            array('.*'),
        );
    }
}
