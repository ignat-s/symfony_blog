<?php

namespace Acme\BlogBundle\Tests\Model;

use Acme\BlogBundle\Document\DomainFactory;

class DomainFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DomainFactory
     */
    private $domainFactory;

    protected function setUp()
    {
        $this->domainFactory = new DomainFactory();
    }

    public function testCreateUser()
    {
        $this->assertInstanceOf('Acme\BlogBundle\Document\User', $this->domainFactory->createUser());
    }

    public function testCreatePost()
    {
        $this->assertInstanceOf('Acme\BlogBundle\Document\Post', $this->domainFactory->createPost());
    }

    public function testCreateComment()
    {
        $this->assertInstanceOf('Acme\BlogBundle\Document\Comment', $this->domainFactory->createComment());
    }
}
