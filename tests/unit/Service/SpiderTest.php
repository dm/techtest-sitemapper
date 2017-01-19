<?php

namespace Tests\Command;

use Sitemapper\Model\Url;
use Sitemapper\Service\Spider;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

/**
 * Class SpiderTest
 * @package Tests\Command
 */
class SpiderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    protected $client;

    /**
     * @var array
     */
    protected $history;

    /**
     * @var MockHandler
     */
    protected $mock;
    /**
     * @var
     */
    protected $spider;

    /**
     * Mock Guzzle responses in spider order we're expecting
     *
     * @param array $responses
     * @return GuzzleClient
     */
    protected function getGuzzle(array $responses = [])
    {
        if (empty($responses)) {
            $responses = [
                new Response(200, ['x-page' => 'home', 'Content-Type' => 'text/html'], '<html><body><h1>Home</h1><a href="/">Home</a><a href="/about">About</a><a href="/contact">Contact</a><a href="http://www.google.com">Google</a></body></html>'),
                new Response(200, ['x-page' => 'home', 'Content-Type' => 'text/html'], '<html><body><h1>Home</h1><a href="/">Home</a><a href="/about">About</a><a href="/contact">Contact</a><a href="http://www.google.com">Google</a></body></html>'),
                new Response(200, ['x-page' => 'about', 'Content-Type' => 'text/html'], '<html><body><h1>About</h1><a href="/">Home</a><a href="/about/work">What We Do</a><a href="/about/careers">Careers</a><a href="/contact">Contact</a><a href="http://www.google.com">Google</a></body></html>'),
                new Response(200, ['x-page' => 'contact', 'Content-Type' => 'text/html'], '<html><body><h1>Contact</h1><a href="/">Home</a></body></html>'),
                new Response(200, ['x-page' => 'about/work', 'Content-Type' => 'text/html'], '<html><body><h1>What We Do</h1><a href="/">Home</a><a href="/contact">Contact</a></body></html>'),
                new Response(200, ['x-page' => 'about/careers', 'Content-Type' => 'text/html'], '<html><body><h1>Careers</h1><a href="/">Home</a><a href="/contact">Contact</a></body></html>'),
            ];
        }
        $this->mock = new MockHandler($responses);
        $handler = HandlerStack::create($this->mock);
        $this->history = [];
        $handler->push(Middleware::history($this->history));
        $guzzle = new GuzzleClient([
            'redirect.disable' => true,
            'base_uri' => '',
            'handler' => $handler
        ]);

        return $guzzle;
    }

    /**
     * Test we can fetch a random Neocities URL
     */
    public function testCanGetRandomNeocities()
    {
        $guzzle = $this->getGuzzle([
            new Response(200, [], '<a href="https://www.google.com">This should be ignored</a><a href="https://testing.neocities.org/" class="neo-Screen-Shot"></a><a href="https://www.google.com">This should be ignored</a><a href="https://www.google.com">This should be ignored</a><a href="https://www.google.com">This should be ignored</a><a href="https://www.google.com">This should be ignored</a><a href="https://www.google.com">This should be ignored</a>'),
        ]);
        $client = new Client();
        $client->setClient($guzzle);
        $spider = new Spider($client);

        $this->assertEquals($spider->getRandomNeocities(), 'https://testing.neocities.org/');
        $this->assertEquals(1, count($this->history));
    }

    /**
     * Test we can fetch a random Neocities URL
     */
    public function testSpiderCrawls()
    {
        $guzzle = $this->getGuzzle();
        $client = new Client();
        $client->setClient($guzzle);
        $spider = new Spider($client);
        $spider->crawl(new Url('http://www.lab.pt/', true));

        $this->assertEquals(5, count($this->history));
    }

    /**
     * Ensure setters/getters and default values for pages limit
     */
    public function testCanGetAndSetPagesLimit()
    {
        $spider = new Spider(new Client());

        $this->assertEquals(50, $spider->getPages());
        $spider->setPages(100);
        $this->assertEquals(100, $spider->getPages());
    }
}
