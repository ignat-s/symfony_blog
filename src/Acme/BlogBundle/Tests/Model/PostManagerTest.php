<?php

namespace Acme\BlogBundle\Tests\Model;

use Acme\BlogBundle\Model\PostManager;
use Acme\BlogBundle\Model\Post;
use Acme\BlogBundle\Model\Comment;
use Acme\BlogBundle\Model\User;

class PostManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $domainFactory;

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
        $this->domainFactory = $this->getMock('Acme\BlogBundle\Model\DomainFactoryInterface');
        $this->repository = $this->getMock('Acme\BlogBundle\Repository\PostRepositoryInterface');
        $this->postManager = new PostManager($this->domainFactory, $this->repository);
    }

    public function testCreatePost()
    {
        $user = $this->createUser();
        $expectedPost = $this->createPost();

        $this->domainFactory->expects($this->once())
            ->method('createPost')
            ->will($this->returnValue($expectedPost));

        $actualPost = $this->postManager->createPost($user);
        $this->assertSame($expectedPost, $actualPost);
        $this->assertSame($user, $expectedPost->getAuthor());
    }

    /**
     * @return User
     */
    private function createUser()
    {
        return $this->getMockForAbstractClass('Acme\BlogBundle\Model\User');
    }

    /**
     * @return Post
     */
    private function createPost()
    {
        return $this->getMockForAbstractClass('Acme\BlogBundle\Model\Post');
    }

    public function testCreatePostComment()
    {
        $expectedComment = $this->createComment();

        $this->domainFactory->expects($this->once())
            ->method('createComment')
            ->will($this->returnValue($expectedComment));

        $actualComment = $this->postManager->createPostComment();
        $this->assertSame($expectedComment, $actualComment);
    }

    /**
     * @return Comment
     */
    private function createComment()
    {
        return $this->getMockForAbstractClass('Acme\BlogBundle\Model\Comment');
    }

    /**
     * @dataProvider updatePermalinkReplacementDataProvider
     * @param string $actualTitle
     * @param string $expectedPermalink
     */
    public function testUpdatePermalinkReplacement($actualTitle, $expectedPermalink)
    {
        $post = $this->createPost();
        $post->setTitle($actualTitle);

        $this->repository->expects($this->once())
            ->method('findOneByPermalink')
            ->with($expectedPermalink)
            ->will($this->returnValue(null));

        $this->postManager->updatePermalink($post);
        $this->assertEquals($expectedPermalink, $post->getPermalink());
    }

    /**
     * @return array
     */
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
        $post = $this->createPost();
        $post->setTitle('Post title');

        $this->repository->expects($this->exactly(4))
            ->method('findOneByPermalink')
            ->will(
                $this->returnValueMap(
                    array(
                        array('Post_title', $this->createPost()),
                        array('Post_title_1', $this->createPost()),
                        array('Post_title_2', $this->createPost()),
                        array('Post_title_3', null),
                    )
                )
            );

        $this->postManager->updatePermalink($post);
        $this->assertEquals('Post_title_3', $post->getPermalink());
    }

    public function testUpdatePermalinkWhenItHasSameValue()
    {
        $post = $this->createPost();
        $post->setTitle('Post title');

        $this->repository->expects($this->once(4))
            ->method('findOneByPermalink')
            ->with('Post_title')
            ->will($this->returnValue($post));

        $this->postManager->updatePermalink($post);
        $this->assertEquals('Post_title', $post->getPermalink());
    }
}
