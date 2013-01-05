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

    public function testId()
    {
        $this->assertNull($this->post->getId());

        $propertyReflection = new \ReflectionProperty($this->post, 'id');
        $propertyReflection->setAccessible(true);
        $id = new \MongoId('test');
        $propertyReflection->setValue($this->post, $id);

        $this->assertEquals($id, $this->post->getId());
    }

    public function testBody()
    {
        $this->assertNull($this->post->getBody());

        $this->post->setBody('text');
        $this->assertEquals('text', $this->post->getBody());
    }
}
