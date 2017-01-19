<?php

namespace Sitemapper\Command;

use Symfony\Component\Console\Command\Command;

class Sitemapper extends Command
{
    protected function configure()
    {
        $this->setName('sitemapper')->setDescription('Site mapper');
    }
}
