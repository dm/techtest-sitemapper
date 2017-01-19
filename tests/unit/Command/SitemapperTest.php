<?php

namespace Tests\Command;

use Sitemapper\Command\Sitemapper;

class SitemapperTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialize()
    {
        $sitemapper = new Sitemapper();
        $this->isInstanceOf('\Symfony\Component\Console\Command\Command', $sitemapper);
    }

    public function testValidCommandName()
    {
        $sitemapper = new Sitemapper();
        $this->assertEquals('sitemapper', $sitemapper->getName());
    }
}
