<?php

namespace Tests\Model;

use Sitemapper\Exception\InvalidUrlException;
use Sitemapper\Model\Url;

class UrlTest extends \PHPUnit_Framework_TestCase
{
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
     * @expectedException \Sitemapper\Exception\InvalidUrlException
     */
    public function testInitializeInvalidUrl()
    {
        new Url('', true);
    }

    /**
     * @expectedException  \Sitemapper\Exception\InvalidUrlException
     */
    public function testInitializeInvalidUrlWithPath()
    {
        new Url('/login/', true);
    }

    public function testGetsDomainFromUrl()
    {
        $url = new Url('https://host.example.com/login/user/', true);
        $host = $url->getDomainFromUrl();
        $this->assertSame('host.example.com', $host);
    }

    public function testCanValidateStrictHost()
    {
        $url = new Url('https://www.example.com/index.html', true);
        $url = new Url('https://www.example.com/login/user/');
        $this->assertTrue($url->isSameHost());

        $url = new Url('https://host.example.com/login/user/', true);
        $url->setSameHost('http://host.example.com/index.html');
        $this->assertTrue($url->isSameHost());

        $url = new Url('https://www.example.com/login/user/', true);
        $url->setSameHost('http://host.example.com/index.html');
        $this->assertFalse($url->isSameHost());
    }

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
    }
}