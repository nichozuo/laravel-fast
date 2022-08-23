<?php

namespace Nichozuo\LaravelFast\Console\Commands;

use Illuminate\Console\Command;
use Nichozuo\LaravelFast\Helpers\TableHelper;
use Psr\SimpleCache\InvalidArgumentException;

class DBCacheCommand extends Command
{
    protected $signature = 'db:cache';
    protected $description = 'clean the db cache files';

    /**
     * @return void
     * @throws InvalidArgumentException
     */
    public function handle(): void
    {
        TableHelper::ReCache();
    }

}