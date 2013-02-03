<?php

namespace Acme\BlogBundle\Tests\Model;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Acme\BlogBundle\Model\DomainFactoryInterface;
use Acme\BlogBundle\Repository\UserRepositoryInterface;
use Acme\BlogBundle\Model\User;
use Acme\BlogBundle\Model\UserManager;

class UserManagerTest extends \PHPUnit_Framework_TestCase
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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $encoderFactory;

    /**
     * @var UserManager
     */
    private $userManager;

    protected function setUp()
    {
        $this->domainFactory = $this->getMock('Acme\BlogBundle\Model\DomainFactoryInterface');
        $this->repository = $this->getMock('Acme\BlogBundle\Repository\UserRepositoryInterface');
        $this->encoderFactory = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $this->userManager = new UserManager($this->domainFactory, $this->repository, $this->encoderFactory);
    }

    public function testCreateUser()
    {
        $expectedUser = $this->createUser();
        $this->domainFactory->expects($this->once())
            ->method('createUser')
            ->will($this->returnValue($expectedUser));
        $actualUser = $this->userManager->createUser();
        $this->assertSame($expectedUser, $actualUser);
        $this->assertTrue($actualUser->hasRole(User::ROLE_USER));
    }

    /**
     * @return User
     */
    private function createUser()
    {
        return $this->getMockForAbstractClass('Acme\BlogBundle\Model\User');
    }

    public function testUpdatePassword()
    {
        $user = $this->createUser();
        $salt = $user->getSalt();
        $plainPassword = 'pa$$word';
        $encodedPassword = sha1($plainPassword);
        $user->setPlainPassword($plainPassword);

        $encoder = $this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');

        $this->encoderFactory->expects($this->once())
            ->method('getEncoder')
            ->with($user)
            ->will($this->returnValue($encoder));

        $encoder->expects($this->once())
            ->method('encodePassword')
            ->with($plainPassword, $salt)
            ->will($this->returnValue($encodedPassword));

        $this->userManager->updatePassword($user);
        $this->assertEquals($encodedPassword, $user->getPassword());
        $this->assertNull($user->getPlainPassword());
    }

    public function testUpdatePasswordNotChange()
    {
        $user = $this->createUser();
        $oldPassword = sha1('pa$$word');
        $user->setPassword($oldPassword);

        $this->encoderFactory->expects($this->never())->method('getEncoder');

        $this->userManager->updatePassword($user);
        $this->assertEquals($oldPassword, $user->getPassword());
    }

    public function testLoadUserByUsername()
    {
        $user = $this->createUser();
        $usernameOrEmail = 'test';

        $this->repository->expects($this->once())
            ->method('findOneByUsernameOrEmail')
            ->with($usernameOrEmail)
            ->will($this->returnValue($user));

        $this->assertSame($user, $this->userManager->loadUserByUsername($usernameOrEmail));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     * @expectedExceptionMessage No user "test" was found.
     */
    public function testLoadUserByUsernameFails()
    {
        $usernameOrEmail = 'test';

        $this->repository->expects($this->once())
            ->method('findOneByUsernameOrEmail')
            ->with($usernameOrEmail)
            ->will($this->returnValue(null));

        $this->userManager->loadUserByUsername($usernameOrEmail);
    }

    public function testRefreshUser()
    {
        $user = $this->createUser();
        $userId = 'user_id';
        $user->setId($userId);

        $refreshedUser = clone $user;

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('id' => $userId))
            ->will($this->returnValue($refreshedUser));

        $this->assertSame($refreshedUser, $this->userManager->refreshUser($user));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     * @expectedExceptionMessage User with ID "user_id" could not be reloaded.
     */
    public function testRefreshUserFails()
    {
        $user = $this->createUser();
        $userId = 'user_id';
        $user->setId($userId);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('id' => $userId))
            ->will($this->returnValue(null));

        $this->userManager->refreshUser($user);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     * @expectedExceptionMessage Account Acme_BlogBundle_Tests_Model_TestRefreshUser is not support.
     */
    public function testRefreshUserClassNotSupport()
    {
        $user = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')
            ->setMockClassName('Acme_BlogBundle_Tests_Model_TestRefreshUser')
            ->getMock();
        $this->userManager->refreshUser($user);
    }

    public function testSupportsClass()
    {
        $mockClass = $this->getMockClass('Acme\BlogBundle\Document\User');
        $this->assertTrue($this->userManager->supportsClass($mockClass));
        $this->assertTrue($this->userManager->supportsClass('Acme\BlogBundle\Document\User'));
        $this->assertFalse($this->userManager->supportsClass('Acme\BlogBundle\Model\User'));
        $this->assertFalse($this->userManager->supportsClass('User'));
    }
}
