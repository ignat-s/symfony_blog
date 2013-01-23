<?php

namespace Acme\BlogBundle\Tests\Form\Model;

use Acme\BlogBundle\Model\User;
use Acme\BlogBundle\Form\Model\Registration;

class RegistrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Registration
     */
    private $registration;

    protected function setUp()
    {
        $this->user = $this->createUser();
        $this->registration = new Registration($this->user);
    }

    /**
     * @return User
     */
    private function createUser()
    {
        return $this->getMockForAbstractClass('Acme\BlogBundle\Model\User');
    }

    public function testConstructorDefaultValues()
    {
        $this->assertSame($this->user, $this->registration->getUser());
        $this->assertFalse($this->registration->getTermsAccepted());
    }

    public function testUser()
    {
        $user = $this->createUser();
        $this->registration->setUser($user);
        $this->assertSame($user, $this->registration->getUser());
    }

    public function testTermsAccepted()
    {
        $this->registration->setTermsAccepted(true);
        $this->assertTrue($this->registration->getTermsAccepted());
    }
}
