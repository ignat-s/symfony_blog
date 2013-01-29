<?php

namespace Acme\BlogBundle\Tests\Controller;

use Acme\BlogBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $this->setCrawler($crawler);

        $this->assertContains('Blog Posts', $crawler->filter('title')->text());
        $this->assertCrawlerHasNode('h2 a:contains("Unit testing")');
        $this->assertCrawlerHasNode('h2 a:contains("Functional testing")');
    }

    public function testPostsByTagAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tag/unit testing');
        $this->setCrawler($crawler);

        $this->assertContains('Posts By Tag: unit testing', $crawler->filter('title')->text());
        $this->assertCrawlerHasNode('h2 a:contains("Unit testing")');
        $this->assertCrawlerNotHasNode('h2 a:contains("Functional testing")');
    }

    public function testCreateAction()
    {
        $client = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'user',
                'PHP_AUTH_PW'   => 'userpass',
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

    public function testCreateActionSubmitSuccessful()
    {
        $client = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'user',
                'PHP_AUTH_PW'   => 'userpass',
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

    /**
     * @dataProvider createActionSubmitErrorsDataProvider
     * @param array $postData
     * @param array|string $errorMessages
     * @return void
     */
    public function testCreateActionSubmitErrors(array $postData, $errorMessages)
    {
        $postData = array_merge(
            array(
                'post[title]' => 'Test post',
                'post[body]' => 'This post created by automated test.',
            ),
            $postData
        );

        $client = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'user',
                'PHP_AUTH_PW'   => 'userpass',
            )
        );

        $crawler = $client->request('GET', '/posts/create');

        $form = $crawler->selectButton('Submit')->form($postData);

        $crawler = $client->submit($form);
        $this->assertFalse($client->getResponse()->isRedirection());
        $this->setCrawler($crawler);
        foreach ((array)$errorMessages as $errorMessage) {
            $this->assertCrawlerHasNode("span:contains(\"$errorMessage\")");
        }
    }

    public function createActionSubmitErrorsDataProvider()
    {
        return array(
            'title is required' => array(
                array('post[title]' => ''),
                'This value should not be blank.'
            ),
            'body is required' => array(
                array('post[body]' => ''),
                'This value should not be blank.'
            )
        );
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

    public function testShowActionPostNotFound()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/posts/unknown_post');
        $this->setCrawler($crawler);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testAddCommentAction()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/posts/unit_testing');
        $this->setCrawler($crawler);

        $this->assertCrawlerHasNode('form input[type="text"][required="required"][name="comment[author]"]');
        $this->assertCrawlerHasNode('form input[type="email"][required="required"][name="comment[email]"]');
        $this->assertCrawlerHasNode('form textarea[required="required"][name="comment[body]"]');
    }

    public function testAddCommentActionSubmitSuccessful()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/posts/unit_testing');

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

    /**
     * @dataProvider addCommentSubmitErrorsDataProvider
     * @param array $commentData
     * @param array|string $errorMessages
     * @return void
     */
    public function testAddCommentActionSubmitErrors(array $commentData, $errorMessages)
    {
        $commentData = array_merge(
            array(
                'comment[author]' => 'John Doe',
                'comment[email]' => 'john.doe@example.com',
                'comment[body]' => 'This comment created by automated test.',
            ),
            $commentData
        );

        $client = static::createClient();

        $crawler = $client->request('GET', '/posts/unit_testing');

        $form = $crawler->selectButton('Add Comment')->form($commentData);

        $crawler = $client->submit($form);
        $this->assertFalse($client->getResponse()->isRedirection());
        $this->setCrawler($crawler);
        foreach ((array)$errorMessages as $errorMessage) {
            $this->assertCrawlerHasNode("span:contains(\"$errorMessage\")");
        }
    }

    public function addCommentSubmitErrorsDataProvider()
    {
        return array(
            'author is required' => array(
                array('comment[author]' => ''),
                'This value should not be blank.'
            ),
            'email is required' => array(
                array('comment[email]' => ''),
                'This value should not be blank.'
            ),
            'email is invalid' => array(
                array('comment[email]' => 'john'),
                'This value is not a valid email address'
            ),
            'body is required' => array(
                array('comment[body]' => ''),
                'This value should not be blank.'
            )
        );
    }
}
