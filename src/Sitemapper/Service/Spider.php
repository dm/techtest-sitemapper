<?php

namespace Sitemapper\Service;

use Sitemapper\Exception\InvalidUrlException;
use Sitemapper\Model\Sitemap;
use Sitemapper\Model\Url;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class Spider
 * @package Sitemapper\Service
 */
class Spider
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var int
     */
    private $pages = 50;

    /**
     * Sitemap instance
     * @var Sitemap
     */
    private $sitemap;

    /**
     * Crawled pages
     * @var array
     */
    private $seen = [];

    /**
     * @return mixed
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param mixed $pages
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    /**
     * Spider constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Get a random Neocities Url to crawl, can fail silently
     *
     * @return string|false
     * @throws \Exception
     */
    public function getRandomNeocities()
    {
        try {
            $neo = $this->client->request('GET', 'https://neocities.org/browse?sort_by=views');
            $neo->filter('a.neo-Screen-Shot')->each(function (Crawler $node) use (&$urls) {
                $hrefAttr = $node->filter('a')->extract(['href']);
                if (isset($hrefAttr[0])) {
                    try {
                        $url = new Url($hrefAttr[0]);
                        $urls[] = $url->getUrl();
                    } catch (InvalidUrlException $e) {
                    }
                }
            });

            if (sizeof($urls) == 0) {
                throw new \Exception('No URLs to select from Neocities');
            }
            return $urls[array_rand($urls)];
        } catch (RequestException $e) {
            return false;
        }
    }


    /**
     * Crawls site and returns a Sitemap instance
     *
     * @param Url $url
     * @return Sitemap
     */
    public function crawl(Url $url): Sitemap
    {
        $this->sitemap = new Sitemap($url->getUrl());
        $pages = $this->getPages();
        $this->crawler($url, $pages);
        $this->sitemap->createSitemapIndex('/tmp/sitemap2.xml');
        return $this->sitemap;
    }

    /**
     * Actual crawler
     *
     * @param Url $url
     * @param $pages
     * @return true
     */
    private function crawler(Url $url, &$pages)
    {
        if ($pages === 0) {
            return;
        }

        if (isset($this->seen[$url->getUrl()])) {
            return;
        }

        $this->seen[$url->getUrl()] = true;
        $response = $this->client->request('GET', $url->getUrl());

        /** @var \Symfony\Component\BrowserKit\Response $res*/
        $res = $this->client->getResponse();
        if ($res->getStatus() !== 200) {
            return;
        }

        $this->sitemap->addItem(
          $url->isSameHost() ? ltrim($url->getFullPathFromUrl(), '/')
            : $url->getUrl()
        );

        // Crawl more links
        if (substr($res->getHeader('Content-Type'), 0, 9) == 'text/html') {
            $pages--;
            $response->filter('a')->each(function (Crawler $node) use (&$url, &$pages) {
                $href = $node->filter('a')->extract(['href']);
                if (isset($href[0])) {
                    try {
                        $url = new Url($href[0]);
                        if ($url->isSameHost() && !isset($this->seen[$url->getUrl()])) {
                            $this->crawler($url, $pages);
                        }
                    } catch (InvalidUrlException $e) {
                    }
                }
            });
        }
    }
}
