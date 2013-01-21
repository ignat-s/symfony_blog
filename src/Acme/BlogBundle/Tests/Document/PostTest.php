<?php

namespace Acme\BlogBundle\Tests\Document;

use Acme\BlogBundle\Document\User;
use Acme\BlogBundle\Document\Post;
use Acme\BlogBundle\Document\Comment;

class PostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Post
     */
    private $post;

    protected function setUp()
    {
        $this->post = new Post();
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
        $author = new User();
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
        $comment = new Comment();
        $this->post->addComment($comment);
        $this->assertEquals(array($comment), $this->post->getComments());
    }
}
