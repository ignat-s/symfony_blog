<?php

namespace Acme\BlogBundle\Tests\Respository\MongoDB;

use Doctrine\ODM\MongoDB\DocumentManager;
use Acme\BlogBundle\Repository\MongoDB\PostRepository;
use Acme\BlogBundle\Test\WebTestCase;

class PostRepositoryTest extends WebTestCase
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var PostRepository
     */
    private $repository;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->dm = static::$kernel->getContainer()
            ->get('doctrine_mongodb')
            ->getManager();

        $this->repository = $this->dm->getRepository('AcmeBlogBundle:Post');
    }

    /**
     * @dataProvider findOneByPermalinkHasResultDataProvider
     */
    public function testFindOneByPermalinkHasResult($searchPermalink, $expectedTitle, $expectedPermalink)
    {
        $post = $this->repository->findOneByPermalink($searchPermalink);
        $this->assertNotNull($post);
        $this->assertInstanceOf('Acme\BlogBundle\Document\Post', $post);
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
