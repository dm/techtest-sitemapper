<?php

namespace Tests\Model;

use Sitemapper\Exception\InvalidUrlException;
use Sitemapper\Model\Url;

/**
 * Class UrlTest
 * @package Tests\Model
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Validate URL, return and path inheritance for relative/absolute site paths
     */
    public function testInitializeValidUrl()
    {
        $url = new Url('https://www.example.com', true);
        $this->assertEquals('https://www.example.com', $url->getUrl());
        // Keep slash if existing
        $url = new Url('https://www.example.com/', true);
        $this->assertEquals('https://www.example.com/', $url->getUrl());

        // Relative or absolute URLs should match expected root
        $url = new Url('/index.html');
        $this->assertEquals('https://www.example.com/index.html', $url->getUrl());
        $url = new Url('test.html');
        $this->assertEquals('https://www.example.com/test.html', $url->getUrl());

        // Reset host
        $url = new Url('https://www.example.com/site/', true);

        $url = new Url('/index.html');
        $this->assertEquals('https://www.example.com/index.html', $url->getUrl());
        $url = new Url('test.html');
        $this->assertEquals('https://www.example.com/site/test.html', $url->getUrl());
    }

    /**
     * Assert new empty initialization throws InvalidUrlException
     */
    public function testInitializeInvalidUrl()
    {
        $this->expectException(InvalidUrlException::class);
        new Url('', true);
    }

    /**
     * Assert new path only initialization throws InvalidUrlException
     */
    public function testInitializeInvalidUrlWithPath()
    {
        $this->expectException(InvalidUrlException::class);
        new Url('/login/', true);
    }

    /**
     * Ensure we can get the correct host
     */
    public function testGetsDomainFromUrl()
    {
        $url = new Url('https://host.example.com/login/user/', true);
        $host = $url->getDomainFromUrl();
        $this->assertSame('host.example.com', $host);

        $url = new Url('https://subdomain.example.com/login/user/', true);
        $host = $url->getDomainFromUrl();
        $this->assertSame('subdomain.example.com', $host);

        // Not supporting contact e-mail links in sites
        $url = new Url('mailto:user@example.com', true);
        $host = $url->getDomainFromUrl();
        $this->assertFalse($host);
    }

    /**
     * Validating strict host comparison (store statically)
     */
    public function testCanValidateStrictHost()
    {
        $url = new Url('https://www.example.com/index.html', true);
        $url = new Url('https://www.example.com/login/user/');
        $this->assertTrue($url->isSameHost());

        // Redefine after initialising a Url instance
        $url = new Url('https://host.example.com/login/user/', true);
        $url->setSameHost('http://host.example.com/index.html');
        $this->assertTrue($url->isSameHost());

        $url = new Url('https://www.example.com/login/user/', true);
        $url->setSameHost();
        $this->assertTrue($url->isSameHost());

        // Ensure expected failures
        $url = new Url('https://www.example.com/login/user/', true);
        $url->setSameHost('http://host.example.com/index.html');
        $this->assertFalse($url->isSameHost());
    }

    /**
     * Getting full paths from Url including relative Urls
     */
    public function testCanGetFullPathFromUrl()
    {
        $url = new Url('https://wwww.example.com/', true);
        $this->assertSame('/', $url->getFullPathFromUrl());

        $url = new Url('https://wwww.example.com/login/user/');
        $this->assertSame('/login/user/', $url->getFullPathFromUrl());

        $url = new Url('/about');
        $this->assertSame('/about', $url->getFullPathFromUrl());

        $url = new Url('/blog?page=1&list=10');
        $this->assertSame('/blog?page=1&list=10', $url->getFullPathFromUrl());

        $url = new Url('/blog?do=view#10');
        $this->assertSame('/blog?do=view#10', $url->getFullPathFromUrl());

        // Started with subdirectory paths
        $url = new Url('https://wwww.example.com/sub/path/', true);
        $this->assertSame('/sub/path/', $url->getFullPathFromUrl());

        // Defined as a root link
        $url = new Url('/index.html');
        $this->assertSame('/index.html', $url->getFullPathFromUrl());

        $url = new Url('blog?page=1&list=10');
        $this->assertSame('/sub/path/blog?page=1&list=10', $url->getFullPathFromUrl());
    }
}
