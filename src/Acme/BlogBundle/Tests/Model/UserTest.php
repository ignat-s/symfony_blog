<?php

namespace Acme\BlogBundle\Tests\Model;

use Acme\BlogBundle\Model\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var User
     */
    private $user;

    protected function setUp()
    {
        $this->user = $this->getMockForAbstractClass('Acme\BlogBundle\Model\User');
    }

    public function testConstructorDefaultValues()
    {
        $this->assertNull($this->user->getId());
        $this->assertNull($this->user->getUsername());
        $this->assertNull($this->user->getEmail());
        $this->assertNull($this->user->getPassword());
        $this->assertNull($this->user->getPlainPassword());

        $this->assertNotEmpty($this->user->getSalt());
        $this->assertInternalType('string', $this->user->getSalt());

        $this->assertEquals(array(), $this->user->getRoles());
    }

    public function testId()
    {
        $id = 'test';
        $this->user->setId($id);
        $this->assertEquals($id, $this->user->getId());
    }

    public function testUsername()
    {
        $username = 'tony';
        $this->user->setUsername($username);
        $this->assertEquals($username, $this->user->getUsername());
    }

    public function testEmail()
    {
        $email = 'tony@mail.org';
        $this->user->setEmail($email);
        $this->assertEquals($email, $this->user->getEmail());
    }

    public function testPassword()
    {
        $password = sha1('pass');
        $this->user->setPassword($password);
        $this->assertEquals($password, $this->user->getPassword());
    }

    public function testPlainPassword()
    {
        $plainPassword = 'pass';
        $this->user->setPlainPassword($plainPassword);
        $this->assertEquals($plainPassword, $this->user->getPlainPassword());
    }

    public function testEraseCredentials()
    {
        $this->user->setPlainPassword('pass');
        $this->user->eraseCredentials();
        $this->assertEmpty($this->user->getPlainPassword());
    }

    /**
     * @dataProvider setRolesDataProvider
     * @param array $actualRoles
     * @param array $expectedRoles
     */
    public function testSetRoles(array $actualRoles, array $expectedRoles)
    {
        $this->user->setRoles($actualRoles);
        $this->assertEquals($expectedRoles, $this->user->getRoles());
    }

    public function setRolesDataProvider()
    {
        return array(
            'set two different roles' => array(
                array('foo', 'bar'),
                array('foo', 'bar')
            ),
            'set two same roles' => array(
                array('foo', 'foo'),
                array('foo')
            ),
            'set case sensitive roles' => array(
                array('foo', 'Foo'),
                array('foo', 'Foo')
            ),
        );
    }

    /**
     * @dataProvider hasRolesDataProvider
     * @param array $actualRoles
     * @param array $expectedRoles
     */
    public function testHasRoles(array $actualRoles, array $expectedRoles)
    {
        $this->user->setRoles($actualRoles);
        foreach ($expectedRoles as $expectedRole => $has) {
            $this->assertEquals($has, $this->user->hasRole($expectedRole));
        }
    }

    public function hasRolesDataProvider()
    {
        return array(
            'has foo and bar roles' => array(
                array('foo', 'bar'),
                array('foo' => true, 'bar' => true, 'baz' => false, 'qux' => false)
            ),
            'has baz and qux roles' => array(
                array('baz', 'qux'),
                array('foo' => false, 'bar' => false, 'baz' => true, 'qux' => true)
            )
        );
    }

    /**
     * @dataProvider removeRolesDataProvider
     * @param array $actualRoles
     * @param array $removeRoles
     * @param array $expectedRoles
     */
    public function testRemoveRoles(array $actualRoles, array $removeRoles, array $expectedRoles)
    {
        $this->user->setRoles($actualRoles);
        foreach ($removeRoles as $removeRole) {
            $this->user->removeRole($removeRole);
        }
        $this->assertEquals($expectedRoles, $this->user->getRoles());
    }

    public function removeRolesDataProvider()
    {
        return array(
            'remove one role' => array(
                array('foo', 'bar'),
                array('foo'),
                array('bar')
            ),
            'remove one role twice' => array(
                array('foo', 'bar'),
                array('foo', 'foo'),
                array('bar')
            ),
            'remove nonexistent role' => array(
                array('foo', 'bar'),
                array('baz'),
                array('foo', 'bar')
            ),
            'remove all role' => array(
                array('bar', 'qux'),
                array('bar', 'qux'),
                array()
            ),
        );
    }
}
