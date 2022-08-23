<?php

namespace Nichozuo\LaravelFast\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DbBackupCommand extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'iseed backup command';

    public function handle()
    {
        $list = config('common.iSeedBackupList', []);
        foreach ($list as $item) {
            $this->line("backup:::$item");
            Artisan::call("iseed $item --force");
        }
    }
}
