<?php

namespace Acme\BlogBundle\Test;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseTestCase;

class WebTestCase extends BaseTestCase
{
    /**
     * @var Crawler
     */
    private $crawler;

    protected function tearDown()
    {
        parent::tearDown();
        $this->crawler = null;
    }

    protected function setCrawler(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    protected function assertCrawlerHasNode($selector, $message = '')
    {
        if (!$this->crawler) {
            throw new \RuntimeException('Crawler object is not set.');
        }
        if (!$message) {
            $message = "Crawler doesn't have node: $selector";
        }
        $this->assertGreaterThan(0, $this->crawler->filter($selector)->count(), $message);
    }
}
