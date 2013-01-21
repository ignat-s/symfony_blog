<?php

namespace Acme\BlogBundle\Tests\Document;

use Acme\BlogBundle\Document\User;
use Acme\BlogBundle\Document\Comment;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Comment
     */
    private $comment;

    protected function setUp()
    {
        $this->comment = new Comment();
    }

    public function testConstructorDefaultValues()
    {
        $this->assertNull($this->comment->getEmail());
        $this->assertNull($this->comment->getAuthor());
        $this->assertNull($this->comment->getBody());
        $this->assertInstanceOf('DateTime', $this->comment->getCreatedAt());
    }

    public function testEmail()
    {
        $email = 'john.doe@example.com';
        $this->comment->setEmail($email);
        $this->assertEquals($email, $this->comment->getEmail());
    }

    public function testAuthor()
    {
        $author = 'john.doe';
        $this->comment->setAuthor($author);
        $this->assertEquals($author, $this->comment->getAuthor());
    }

    public function testBody()
    {
        $body = 'text';
        $this->comment->setBody($body);
        $this->assertEquals($body, $this->comment->getBody());
    }
}
