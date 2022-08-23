<?php

namespace Nichozuo\LaravelFast\Console\Commands;

use Doctrine\DBAL\Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Nichozuo\LaravelFast\Helpers\DbalHelper;
use Nichozuo\LaravelFast\Helpers\GenHelper;
use Nichozuo\LaravelFast\Helpers\TableHelper;

class DumpTableCommand extends Command
{
    protected $signature = 'dt {table}';
    protected $description = 'dump the fields of the table';

    /**
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        DbalHelper::register();

        $tableName = (string)Str::of($this->argument('table'))->snake()->plural();
        $table = TableHelper::GetTable($tableName);
        $columns = TableHelper::GetTableColumns($table);

        $this->warn('生成 Table 模板');
        $this->line(GenHelper::GenTableString($table));
        $this->line(GenHelper::GenTableCommentString($table));
        $this->line(GenHelper::GenTableFillableString($columns));

        $this->warn('生成 Validate 模板');
        $this->line(GenHelper::GenColumnsRequestValidateString($columns));

        $this->warn('生成 Insert 模板');
        $this->line(GenHelper::GenColumnsInsertString($columns));
    }
}