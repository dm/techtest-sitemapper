<?php

namespace Tests\Model;

use Sitemapper\Model\Sitemap;
use Sitemapper\Model\Url;

/**
 * Class SitemapTest
 * @package Tests\Model
 */
class SitemapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Can initialize Sitemap and correct Instance/Inheritance
     */
    public function testInitializeValidSitemap()
    {
        $url = new Url('https://2bit.neocities.org/');
        $sitemap = new Sitemap($url);
        $this->assertInstanceOf('\Sitemapper\Model\Sitemap', $sitemap);
        $this->assertInstanceOf('\SitemapPHP\Sitemap', $sitemap);
    }
}
