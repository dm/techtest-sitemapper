<?php

namespace Sitemapper\Command;

use Goutte\Client;
use Sitemapper\Model\Url;
use Sitemapper\Service\Spider;
use Sitemapper\Service\Scraper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Sitemapper extends Command
{
    /**
     * Limit spider crawl number of pages
     */
    const LIMIT_PAGES = 100;

    /**
     * @var Crawler
     */
    protected $spider;

    /**
     * @var Scraper
     */
    protected $scraper;

    /**
     * Sitemapper constructor
     */
    public function __construct()
    {
        $client = new Client();
        $this->spider = new Spider($client );

        parent::__construct();
    }

    /**
     * Sitemapper configure command
     */
    protected function configure()
    {
        $this->setName('sitemapper')
            ->setDescription('Site mapper')
            ->addOption('pages', 'p', InputOption::VALUE_OPTIONAL)
            ->addArgument('url', InputArgument::OPTIONAL, 'Domain to scrape');
    }

    /**
     * Execute the Sitemapper command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('#### Generating Sitemap ####');
        $urlStr = $input->getArgument('url') ? $input->getArgument('url') : $this->spider->getRandomNeocities();
        $output->writeln('For domain: ' . $urlStr);
        $url = new Url($urlStr);
        $pages = $input->getOption('pages') ?: self::LIMIT_PAGES;
        $this->spider->setPages($pages);

        try {
            $sitemap = $this->spider->crawl($url);
        } catch (\Exception $e) {
            return $output->writeln('Something went wrong: '.$e->getMessage());
        }

        return $output->writeln($sitemap->toString());
    }
}
