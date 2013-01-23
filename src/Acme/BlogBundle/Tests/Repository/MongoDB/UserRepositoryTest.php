<?php

namespace Acme\BlogBundle\Tests\Respository\MongoDB;

use Doctrine\ODM\MongoDB\DocumentManager;
use Acme\BlogBundle\Repository\MongoDB\UserRepository;
use Acme\BlogBundle\Test\WebTestCase;

class UserRepositoryTest extends WebTestCase
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->dm = static::$kernel->getContainer()
            ->get('doctrine_mongodb')
            ->getManager();

        $this->repository = $this->dm->getRepository('AcmeBlogBundle:User');
    }

    /**
     * @dataProvider getUserData
     * @param string $searchUsername
     * @param string $expectedEmail
     */
    public function testFindOneByUsername($searchUsername, $expectedEmail)
    {
        $user = $this->repository->findOneByUsername($searchUsername);
        $this->assertNotNull($user);
        $this->assertInstanceOf('Acme\BlogBundle\Document\User', $user);
        $this->assertEquals($searchUsername, $user->getUsername());
        $this->assertEquals($expectedEmail, $user->getEmail());
    }

    public function getUserData()
    {
        return array(
            array('admin', 'admin@example.com'),
            array('user', 'user@example.com'),
        );
    }

    public function testFindOneByUsernameEmptyResult()
    {
        $this->assertNull($this->repository->findOneByUsername('foo'));
    }

    /**
     * @dataProvider getUserData
     * @param string $expectedUsername
     * @param string $searchEmail
     */
    public function testFindOneByEmail($expectedUsername, $searchEmail)
    {
        $user = $this->repository->findOneByEmail($searchEmail);
        $this->assertNotNull($user);
        $this->assertInstanceOf('Acme\BlogBundle\Document\User', $user);
        $this->assertEquals($searchEmail, $user->getEmail());
        $this->assertEquals($expectedUsername, $user->getUsername());
    }

    public function testFindOneByEmailEmptyResult()
    {
        $this->assertNull($this->repository->findOneByEmail('foo@example.com'));
    }

    /**
     * @dataProvider getUserData
     * @param string $username
     * @param string $email
     */
    public function testFindOneByUsernameOrEmail($username, $email)
    {
        $user = $this->repository->findOneByUsernameOrEmail($username);
        $this->assertNotNull($user);
        $this->assertInstanceOf('Acme\BlogBundle\Document\User', $user);
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($user, $this->repository->findOneByEmail($email));
    }

    public function testFindOneByUsernameOrEmailEmptyResult()
    {
        $this->assertNull($this->repository->findOneByUsernameOrEmail('foo'));
        $this->assertNull($this->repository->findOneByUsernameOrEmail('foo@example.com'));
    }
}
