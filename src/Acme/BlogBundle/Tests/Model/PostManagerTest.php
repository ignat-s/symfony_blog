<?php

namespace Acme\BlogBundle\Tests\Model;

use Acme\BlogBundle\Document\Post;
use Acme\BlogBundle\Model\PostManager;

class PostManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * @var PostManager
     */
    private $postManager;

    protected function setUp()
    {
        $this->repository = $this->getMock('Acme\BlogBundle\Repository\PostRepositoryInterface');
        $this->postManager = new PostManager($this->repository);
    }

    /**
     * @dataProvider updatePermalinkReplacementDataProvider
     */
    public function testUpdatePermalinkReplacement($actualTitle, $expectedPermalink)
    {
        $post = new Post();
        $post->setTitle($actualTitle);

        $this->repository->expects($this->once())
            ->method('findOneByPermalink')
            ->with($expectedPermalink)
            ->will($this->returnValue(null));

        $this->postManager->updatePermalink($post);
        $this->assertEquals($expectedPermalink, $post->getPermalink());
    }

    public function updatePermalinkReplacementDataProvider()
    {
        return array(
            array('Simple', 'Simple'),
            array('Title  With  Whitespaces', 'Title__With__Whitespaces'),
            array('Title, With! Special `"\'Characters\\?&%$*[]()', 'Title_With_Special_Characters'),
        );
    }

    public function testUpdatePermalinkRecursion()
    {
        $post = new Post();
        $post->setTitle('Post title');

        $this->repository->expects($this->exactly(4))
            ->method('findOneByPermalink')
            ->will(
                $this->returnValueMap(
                    array(
                        array('Post_title', new Post()),
                        array('Post_title_1', new Post()),
                        array('Post_title_2', new Post()),
                        array('Post_title_3', null),
                    )
                )
            );

        $this->postManager->updatePermalink($post);
        $this->assertEquals('Post_title_3', $post->getPermalink());
    }

    public function testUpdatePermalinkWhenItHasSameValue()
    {
        $post = new Post();
        $post->setTitle('Post title');

        $this->repository->expects($this->once(4))
            ->method('findOneByPermalink')
            ->with('Post_title')
            ->will($this->returnValue($post));

        $this->postManager->updatePermalink($post);
        $this->assertEquals('Post_title', $post->getPermalink());
    }
}
