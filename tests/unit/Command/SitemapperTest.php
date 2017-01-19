<?php

namespace Tests\Command;

use Sitemapper\Command\Sitemapper;

/**
 * Class SitemapperTest
 * @package Tests\Command
 */
class SitemapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Initializes an instance of Symfony Command
     */
    public function testInitialize()
    {
        $sitemapper = new Sitemapper();
        $this->isInstanceOf('\Symfony\Component\Console\Command\Command', $sitemapper);
    }

    /**
     * Ensure Sitemapper returns a valid command name
     */
    public function testValidCommandName()
    {
        $sitemapper = new Sitemapper();
        $this->assertEquals('sitemapper', $sitemapper->getName());
    }
}
