<?php

namespace Acme\BlogBundle\Tests\Document;

use Acme\BlogBundle\Document\Post;

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
}
