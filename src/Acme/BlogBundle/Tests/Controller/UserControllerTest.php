<?php

namespace Acme\BlogBundle\Tests\Controller;

use Acme\BlogBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testSignUpAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/signup');
        $this->setCrawler($crawler);

        $this->assertContains('Sign Up', $crawler->filter('title')->text());
        $this->assertCrawlerHasNode(
            'form input[type="text"][required="required"][name="registration[user][username]"]'
        );
        $this->assertCrawlerHasNode('form input[type="email"][required="required"][name="registration[user][email]"]');
        $this->assertCrawlerHasNode(
            'form input[type="password"][required="required"][name="registration[user][plainPassword][password]"]'
        );
        $this->assertCrawlerHasNode(
            'form input[type="password"][required="required"][name="registration[user][plainPassword][confirm]"]'
        );
        $this->assertCrawlerHasNode('form input[type="checkbox"][required="required"][name="registration[terms]"]');
    }

    public function testSignUpActionSubmitNewUser()
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

        $this->assertCrawlerHasNode('.content:contains("Welcome")');
        $this->assertCrawlerHasNode('.content:contains("Posts")');
    }

    /**
     * @dataProvider signUpActionSubmitErrorsDataProvider
     * @param array $signInData
     * @param array|string $errorMessages
     * @return void
     */
    public function testSignUpActionSubmitErrors(array $signInData, $errorMessages)
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
            $this->assertCrawlerHasNode("span:contains(\"$errorMessage\")");
        }
    }

    public function signUpActionSubmitErrorsDataProvider()
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
     * @dataProvider loginActionDataProvider
     */
    public function testLoginAction($username, $password, $expectedNodes)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->setCrawler($crawler);
        $this->assertCrawlerHasNode('form input[type="text"][required="required"][name="_username"]');
        $this->assertCrawlerHasNode('form input[type="password"][required="required"][name="_password"]');

        $form = $crawler->selectButton('Sign In')->form(
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

    public function loginActionDataProvider()
    {
        return array(
            'admin login' => array(
                'admin', 'adminpass', ':contains("Posts")'
            ),
            'bad credentials' => array(
                'admin', 'wrongpassword', '.content:contains("Bad credentials")'
            )
        );
    }
}
