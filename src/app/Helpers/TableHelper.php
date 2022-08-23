<?php


namespace Nichozuo\LaravelFast\Helpers;


use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Psr\SimpleCache\InvalidArgumentException;

class TableHelper
{
    /**
     * @return AbstractSchemaManager
     */
    private static function SM(): AbstractSchemaManager
    {
        return DB::connection()->getDoctrineSchemaManager();
    }

    /**
     * @param string $tableName
     * @param string $comment
     * @return void
     */
    public static function SetComment(string $tableName, string $comment): void
    {
        DB::statement("ALTER TABLE `$tableName` comment '$comment'");
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     */
    public static function ReCache(): void
    {
        Cache::store('file')->delete('list_tables');
        self::GetTables();
    }

    /**
     * @return Table[]
     */
    public static function GetTables(): array
    {
        return Cache::store('file')->rememberForever('list_tables', function () {
            DbalHelper::register();
            return self::SM()->ListTables();
        });
    }

    /**
     * @param string $tableName
     * @return Table|null
     */
    public static function GetTable(string $tableName): ?Table
    {
        foreach (self::GetTables() as $table) {
            if ($table->getName() == $tableName)
                return $table;
        }
        return null;
    }

    /**
     * @param Table|null $table
     * @return array|null
     */
    public static function GetTableColumns(?Table $table): ?array
    {
        if (!$table)
            return null;

        $columns = $table->getColumns();
        $skipColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];
        foreach ($skipColumns as $column) {
            unset($columns[$column]);
        }
        return $columns;
    }

    /**
     * @param Column $column
     * @return string
     */
    public static function GetColumnRequired(Column $column): string
    {
        return ($column->getNotNull()) ? 'required' : 'nullable';
    }

    /**
     * @param Column $column
     * @return mixed
     */
    public static function GetColumnType(Column $column): mixed
    {
        $type = $column->getType()->getName();
        $columnTypes = config('common.dbTypeToPHPType');
        return isset($columnTypes[$type]) ? $columnTypes[$type] : null;
    }

    /**
     * @param array $columns
     * @return bool
     */
    public static function GetColumnsHasSoftDelete(array $columns): bool
    {
        return in_array('deleted_at', array_keys($columns));
    }
}