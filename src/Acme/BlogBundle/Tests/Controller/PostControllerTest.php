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

        $this->assertCrawlerHasNode('h2:contains("Test post")');
        $this->assertCrawlerHasNode(':contains("This post created by automated test.")');
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

        $this->assertCrawlerHasNode('h2:contains("Unit testing")');
        $this->assertCrawlerHasNode(
            'body:contains("The goal of unit testing is to isolate each part '
            . 'of the program and show that the individual parts are correct.")'
        );
    }
}
