<?php


namespace Nichozuo\LaravelFast;


use Nichozuo\LaravelFast\Console\Commands;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->commands([
            Commands\DbBackupCommand::class,
            Commands\DBCacheCommand::class,
            Commands\DumpTableCommand::class,
            Commands\GenFilesCommand::class,
            Commands\RenameMigrationFiles::class,
            Commands\UpdateModelsCommand::class
        ]);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../resources/docs' => public_path('docs'),
            __DIR__ . '/../config/common.php' => config_path('common.php')
        ]);
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }
}