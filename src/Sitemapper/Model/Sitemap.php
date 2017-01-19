<?php

namespace Sitemapper\Model;

use \SitemapPHP\Sitemap as SitemapPHP;

/**
 * Class Sitemap
 * @package Sitemapper\Model
 */
class Sitemap extends SitemapPHP
{
    /**
     * Sitemap constructor.
     * @param $domain
     */
    public function __construct($domain)
    {
        // TODO: Fork SitemapPHP to use XMLWriter::openMemory??
        $this->setPath('/tmp/');
        parent::__construct($domain);
    }

    /**
     * Return contents of sitemap.xml as str
     * @return string
     */
    public function toString()
    {
        $str = file_get_contents('/tmp/sitemap.xml');
        return $str;
    }
}
