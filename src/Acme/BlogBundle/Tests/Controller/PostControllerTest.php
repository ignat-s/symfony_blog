<?php

namespace Acme\BlogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/posts/create');

        $this->assertTrue($crawler->filter('html:contains("Created post id")')->count() > 0);
    }
}
