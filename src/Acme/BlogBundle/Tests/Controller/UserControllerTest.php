<?php

namespace Acme\BlogBundle\Tests\Controller;

use Acme\BlogBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testSignUp()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/signup');
        $this->setCrawler($crawler);

        $this->assertContains('Sign up', $crawler->filter('title')->text());
        $this->assertCrawlerHasNode(
            'form input[type="text"][required="required"][name="registration[user][username]"]'
        );
        $this->assertCrawlerHasNode(
            'form input[type="email"][required="required"][name="registration[user][email]"]'
        );
        $this->assertCrawlerHasNode(
            'form input[type="password"][required="required"][name="registration[user][plainPassword][password]"]'
        );
        $this->assertCrawlerHasNode(
            'form input[type="password"][required="required"][name="registration[user][plainPassword][confirm]"]'
        );
        $this->assertCrawlerHasNode(
            'form input[type="checkbox"][required="required"][name="registration[terms]"]'
        );
    }

    public function testSignUpNewUser()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/signup');

        $form = $crawler->selectButton('Submit')->form(
            array(
                'registration[user][username]' => 'john',
                'registration[user][email]' => 'john@example.com',
                'registration[user][plainPassword][password]' => 'pa$$word',
                'registration[user][plainPassword][confirm]' => 'pa$$word',
                'registration[terms]' => true,
            )
        );

        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->setCrawler($crawler);

        $this->assertCrawlerHasNode('h2:contains("Welcome, john!")');
        $this->assertCrawlerHasNode('a:contains("Goto Blog Home")');
        $this->assertCrawlerHasNode('a:contains("Create a New Post")');
        $this->assertCrawlerHasNode('a:contains("Logout")');
    }

    /**
     * @dataProvider signUpErrorsDataProvider
     * @param array $signInData
     * @param array|string $errorMessages
     * @return void
     */
    public function testSignUpErrors(array $signInData, $errorMessages)
    {
        $signInData = array_merge(
            array(
                'registration[user][username]' => 'john',
                'registration[user][email]' => 'john@example.com',
                'registration[user][plainPassword][password]' => 'pa$$word',
                'registration[user][plainPassword][confirm]' => 'pa$$word',
                'registration[terms]' => true,
            ),
            $signInData
        );

        $client = static::createClient();
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/signup');

        $form = $crawler->selectButton('Submit')->form($signInData);

        $crawler = $client->submit($form);
        $this->assertFalse($client->getResponse()->isRedirection());
        $this->setCrawler($crawler);
        foreach ((array)$errorMessages as $errorMessage) {
            $this->assertCrawlerHasNode("ul li:contains(\"$errorMessage\")");
        }
    }

    public function signUpErrorsDataProvider()
    {
        return array(
            'username is required' => array(
                array('registration[user][username]' => ''),
                'This value should not be blank.'
            ),
            'email is required' => array(
                array('registration[user][email]' => ''),
                'This value should not be blank.'
            ),
            'password is required' => array(
                array(
                    'registration[user][plainPassword][password]' => '',
                    'registration[user][plainPassword][confirm]' => ''
                ),
                'This value should not be blank.'
            ),
            'password mismatch' => array(
                array(
                    'registration[user][plainPassword][password]' => 'foo',
                    'registration[user][plainPassword][confirm]' => 'bar'
                ),
                'Password mismatch.'
            ),
            'terms is required' => array(
                array('registration[terms]' => false),
                'You must accept terms.'
            ),
            'email is invalid' => array(
                array('registration[user][email]' => 'john'),
                'This value is not a valid email address'
            ),
        );
    }

    /**
     * @dataProvider loginDataProvider
     */
    public function testLogin($username, $password, $expectedNodes)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->setCrawler($crawler);
        $this->assertCrawlerHasNode('form input[type="text"][required="required"][name="_username"]');
        $this->assertCrawlerHasNode('form input[type="password"][required="required"][name="_password"]');

        $form = $crawler->selectButton('Login')->form(
            array(
                '_username' => $username,
                '_password' => $password,
            )
        );

        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->setCrawler($crawler);
        $this->assertCrawlerHasNode($expectedNodes);
    }

    public function loginDataProvider()
    {
        return array(
            'admin login' => array(
                'admin', 'adminpass', 'html:contains("This is a place holder for the blog!")'
            ),
            'bad credentials' => array(
                'admin', 'wrongpassword', 'html:contains("Bad credentials")'
            )
        );
    }
}
