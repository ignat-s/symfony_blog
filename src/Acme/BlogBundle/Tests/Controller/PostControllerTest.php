<?php

namespace Acme\BlogBundle\Tests\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Acme\BlogBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testCreateAction()
    {
        $client = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW'   => 'adminpass',
            )
        );

        $crawler = $client->request('GET', '/posts/create');
        $this->setCrawler($crawler);

        $this->assertContains('Create a new post', $crawler->filter('title')->text());
        $this->assertCrawlerHasNode('form input[type="text"][required="required"][name="post[title]"]');
        $this->assertCrawlerHasNode('form textarea[required="required"][name="post[body]"]');
        $this->assertCrawlerHasNode('form select[required="required"][name="post[publicationDate][date][month]"]');
        $this->assertCrawlerHasNode('form select[required="required"][name="post[publicationDate][date][day]"]');
        $this->assertCrawlerHasNode('form select[required="required"][name="post[publicationDate][date][year]"]');
        $this->assertCrawlerHasNode(
            'form select[required="required"][name="post[publicationDate][time][hour]"]'
        );
        $this->assertCrawlerHasNode(
            'form select[required="required"][name="post[publicationDate][time][minute]"]'
        );
    }

    public function testCreateActionSubmitNewPost()
    {
        $client = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW'   => 'adminpass',
            )
        );

        $crawler = $client->request('GET', '/posts/create');

        $form = $crawler->selectButton('Submit')->form(
            array(
                'post[title]' => 'Test post',
                'post[body]' => 'This post created by automated test.',
            )
        );

        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->setCrawler($crawler);

        $this->assertCrawlerHasNode('.content:contains("Test post")');
        $this->assertCrawlerHasNode('.content:contains("This post created by automated test.")');
    }

    public function testCreateActionDeniedForAnonymousUser()
    {
        $client = static::createClient();

        $client->request('GET', '/posts/create');

        $this->assertTrue($client->getResponse()->isRedirection());
        $this->assertContains('/login', $client->getResponse()->getTargetUrl());
    }

    public function testShowAction()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/posts/unit_testing');
        $this->setCrawler($crawler);

        $this->assertCrawlerHasNode('.content:contains("Unit testing")');
        $this->assertCrawlerHasNode(
            '.content:contains("The goal of unit testing is to isolate each part '
            . 'of the program and show that the individual parts are correct.")'
        );
    }

    public function testAddComment()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/posts/unit_testing');
        $this->setCrawler($crawler);

        $this->assertCrawlerHasNode('form input[type="text"][required="required"][name="comment[author]"]');
        $this->assertCrawlerHasNode('form input[type="email"][required="required"][name="comment[email]"]');
        $this->assertCrawlerHasNode('form textarea[required="required"][name="comment[body]"]');

        $form = $crawler->selectButton('Add Comment')->form(
            array(
                'comment[author]' => 'John Doe',
                'comment[email]' => 'john.doe@example.com',
                'comment[body]' => 'This comment created by automated test.',
            )
        );

        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->setCrawler($crawler);

        $this->assertCrawlerHasNode('.content:contains("Unit testing")');
        $this->assertCrawlerHasNode(
            '.content:contains("The goal of unit testing is to isolate each part '
            . 'of the program and show that the individual parts are correct.")'
        );
        $this->assertCrawlerHasNode('.content a[href="email:john.doe@example.com"]:contains("John Doe")');
        $this->assertCrawlerHasNode('.content:contains("This comment created by automated test.")');
    }
}
