<?php

namespace Acme\BlogBundle\Tests\Model;

use Acme\BlogBundle\Model\User;
use Acme\BlogBundle\Model\Comment;
use Acme\BlogBundle\Model\Post;

class PostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Post
     */
    private $post;

    protected function setUp()
    {
        $this->post = $this->createPost();
    }

    /**
     * @param string|null $id
     * @param string|null $title
     * @return Post|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createPost($id = null, $title = null)
    {
        $result = $this->getMockForAbstractClass('Acme\BlogBundle\Model\Post');
        if (!is_null($id)) {
            $result->setId($id);
        }
        if (!is_null($title)) {
            $result->setTitle($title);
        }
        return $result;
    }

    public function testConstructorDefaultValues()
    {
        $this->assertNull($this->post->getId());
        $this->assertNull($this->post->getTitle());
        $this->assertNull($this->post->getBody());
        $this->assertNull($this->post->getPermalink());
        $this->assertInstanceOf('DateTime', $this->post->getPublicationDate());
        $this->assertEquals(array(), $this->post->getTags());
        $this->assertEquals(array(), $this->post->getComments());
    }

    public function testId()
    {
        $id = 'test';
        $this->post->setId($id);
        $this->assertEquals($id, $this->post->getId());
    }

    public function testTitle()
    {
        $title = 'title';
        $this->post->setTitle($title);
        $this->assertEquals($title, $this->post->getTitle());
    }

    public function testBody()
    {
        $body = 'text';
        $this->post->setBody($body);
        $this->assertEquals($body, $this->post->getBody());
    }

    public function testPermalink()
    {
        $permalink = 'permalink';
        $this->post->setPermalink($permalink);
        $this->assertEquals($permalink, $this->post->getPermalink());
    }

    public function testAuthor()
    {
        $author = $this->getMockForAbstractClass('Acme\BlogBundle\Model\User');
        $this->post->setAuthor($author);
        $this->assertEquals($author, $this->post->getAuthor());
    }

    public function testPublicationDate()
    {
        $date = new \DateTime();
        $this->post->setPublicationDate($date);
        $this->assertEquals($date, $this->post->getPublicationDate());
    }

    /**
     * @dataProvider setTagsDataProvider
     * @param string $actual
     * @param string $expected
     */
    public function testSetTags($actual, $expected)
    {
        $this->post->setTags($actual);
        $this->assertEquals($expected, $this->post->getTags());
    }

    public function setTagsDataProvider()
    {
        return array(
            array(array('foo', 'bar'), array('foo', 'bar')),
            array(array('foo', 'bar', 'Bar'), array('foo', 'bar', 'Bar')),
            array(array('foo', 'bar', 'baz', 'baz'), array('foo', 'bar', 'baz')),
        );
    }

    public function testAddTag()
    {
        $this->post->addTag('TDD');
        $this->post->addTag('unit testing');
        $this->post->addTag('tdd');
        $this->post->addTag('TDD');
        $this->assertEquals(array('TDD', 'unit testing', 'tdd'), $this->post->getTags());
    }

    /**
     * @dataProvider getTagsStringDataProvider
     * @param array $actual
     * @param string $expected
     */
    public function testGetTagsString(array $actual, $expected)
    {
        $this->post->setTags($actual);
        $this->assertEquals($expected, $this->post->getTagsString());
    }

    public function getTagsStringDataProvider()
    {
        return array(
            array(array(), ''),
            array(array('foo', 'bar'), 'foo, bar'),
        );
    }

    /**
     * @dataProvider setTagsStringDataProvider
     * @param string $actual
     * @param array $expected
     */
    public function testSetTagsString($actual, array $expected)
    {
        $this->post->setTagsString($actual);
        $this->assertEquals($expected, $this->post->getTags());
    }

    public function setTagsStringDataProvider()
    {
        return array(
            array('', array()),
            array('foo, bar', array('foo', 'bar')),
            array('foo, bar, bar, baz', array('foo', 'bar', 'baz')),
        );
    }

    public function testComments()
    {
        $comment = $this->getMockForAbstractClass('Acme\BlogBundle\Model\Comment');
        $this->post->addComment($comment);
        $this->assertEquals(array($comment), $this->post->getComments());
    }

    /**
     * @dataProvider equalsToDataProvider
     * @param Post $post
     * @param mixed $otherValue
     * @param boolean $expectedValue
     */
    public function testEqualsTo(Post $post, $otherValue, $expectedValue)
    {
        $this->assertEquals($expectedValue, $post->equalsTo($otherValue));
    }

    public function equalsToDataProvider()
    {
        return array(
            array($this->createPost(1), $this->createPost(1), true),
            array($this->createPost(1), $this->createPost(2), false),
            array($this->createPost(1), new \stdClass(), false),
            array($this->createPost(null, 'Title'), $this->createPost(null, 'Title'), true),
            array($this->createPost(null, 'Title One'), $this->createPost(null, 'Title Two'), false),
        );
    }
}
