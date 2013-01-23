<?php

namespace Acme\BlogBundle\Tests\Model;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Acme\BlogBundle\Repository\UserRepositoryInterface;
use Acme\BlogBundle\Document\User;
use Acme\BlogBundle\Model\UserManager;

class UserManagerTest extends \PHPUnit_Framework_TestCase
{
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
        $this->repository = $this->getMock('Acme\BlogBundle\Repository\UserRepositoryInterface');
        $this->encoderFactory = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $this->userManager = new UserManager($this->repository, $this->encoderFactory);
    }

    public function testCreateUser()
    {
        $user = $this->userManager->createUser();
        $this->assertInstanceOf('Acme\BlogBundle\Document\User', $user);
        $this->assertTrue($user->hasRole(User::ROLE_USER));
    }

    public function testUpdatePassword()
    {
        $user = new User();
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
        $user = new User();
        $oldPassword = sha1('pa$$word');
        $user->setPassword($oldPassword);

        $this->encoderFactory->expects($this->never())->method('getEncoder');

        $this->userManager->updatePassword($user);
        $this->assertEquals($oldPassword, $user->getPassword());
    }

    public function testLoadUserByUsername()
    {
        $user = new User();
        $usernameOrEmail = 'test';

        $this->repository->expects($this->once())
            ->method('findOneByUsernameOrEmail')
            ->with($usernameOrEmail)
            ->will($this->returnValue($user));

        $this->assertSame($user, $this->userManager->loadUserByUsername($usernameOrEmail));
    }

    public function testRefreshUser()
    {
        $user = new User();
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
     * @expectedException \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     * @expectedExceptionMessage Account is not support.
     */
    public function testRefreshUserClassNotSupport()
    {
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $this->userManager->refreshUser($user);
    }

    public function testRefreshUserClassImplement()
    {
        $this->setExpectedException(
            'Symfony\Component\Security\Core\Exception\UnsupportedUserException',
            'Expected an instance of Acme\BlogBundle\Document\User, but got AcmeBlogBundleDocumentTestUser.'
        );

        $userClass = 'AcmeBlogBundleDocumentTestUser';
        $user = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')
            ->setMockClassName($userClass)
            ->getMockForAbstractClass();

        $userManager = new UserManager($this->repository, $this->encoderFactory, $userClass);
        $userManager->refreshUser($user);
    }

    public function testSupportsClass()
    {
        $this->assertTrue($this->userManager->supportsClass('Acme\BlogBundle\Document\User'));
        $this->assertFalse($this->userManager->supportsClass('User'));
    }
}
