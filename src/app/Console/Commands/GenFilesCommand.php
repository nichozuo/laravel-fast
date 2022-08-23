<?php

namespace Nichozuo\LaravelFast\Console\Commands;

use Exception;
use Doctrine\DBAL\Schema\Table;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Str;
use Nichozuo\LaravelFast\Helpers\DbalHelper;
use Nichozuo\LaravelFast\Helpers\GenHelper;
use Nichozuo\LaravelFast\Helpers\ReflectHelper;
use Nichozuo\LaravelFast\Helpers\StubHelper;
use Nichozuo\LaravelFast\Helpers\TableHelper;
use ReflectionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GenFilesCommand extends BaseCommand
{
    protected $name = 'gf';
    protected $description = 'Generate files of the table';

    protected function getArguments(): array
    {
        return [
            ['key', InputArgument::REQUIRED, '表名'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['migration', 'm', InputOption::VALUE_NONE, '创建 migration 文件'],
            ['model', 'd', InputOption::VALUE_NONE, '创建 model 文件'],
            ['controller', 'c', InputOption::VALUE_NONE, '创建 controller 文件'],
            ['test', 't', InputOption::VALUE_NONE, '根据controller创建test文件'],
            ['force', 'f', InputOption::VALUE_NONE, '强制创建并覆盖'],
            ['rules', 'r', InputOption::VALUE_NONE, '单独提取 validate rules'],
        ];
    }

    /**
     * @throws Exception
     * @throws ReflectionException
     */
    public function handle()
    {
        DbalHelper::register();
        $options = $this->options();
        // 计算table和prefix
        $key = $this->argument('key');
        $key = str_replace('/', '\\', $key);
        $prefix = explode('\\', $key);
        $table = end($prefix);
        array_pop($prefix);
        $prefix = array_map(function ($item) {
            return Str::of($item)->studly();
        }, $prefix);

        $tableName = (string)Str::of($table)->snake()->plural();
        $modelName = (string)Str::of($tableName)->studly();

        $table = TableHelper::GetTable($tableName);
        $columns = TableHelper::GetTableColumns($table);

        if ($options['migration'])
            $this->makeMigration($tableName);

        if ($options['model'])
            $this->makeModel($table, $columns, $modelName, $options);

        if ($options['controller'])
            $this->makeController($table, $columns, $modelName, $prefix, $options);

        if ($options['test'])
            $this->makeTest($modelName, $prefix, $options);


    }

    /**
     * @param string $tableName
     */
    private function makeMigration(string $tableName)
    {
        $this->call('make:migration', [
            'name' => "create_{$tableName}_table",
            '--create' => $tableName,
            '--table' => $tableName,
        ]);
    }

    /**
     * @param Table $table
     * @param array $columns
     * @param string $modelName
     * @param array $options
     */
    private function makeModel(Table $table, array $columns, string $modelName, array $options)
    {
        $hasSoftDelete = TableHelper::GetColumnsHasSoftDelete($table->getColumns());
        // BaseModel
        $stubName = $hasSoftDelete ? 'BaseModelWithSoftDelete.stub' : 'BaseModel.stub';
        $stubContent = StubHelper::GetStub($stubName);
        $stubContent = StubHelper::Replace([
            '{{ModelProperties}}' => GenHelper::GenColumnsPropertiesString($table),
            '{{ModelMethods}}' => GenHelper::GenTableMethodsString(),
            '{{ModelName}}' => $modelName,
            '{{TableString}}' => GenHelper::GenTableString($table),
            '{{TableCommentString}}' => GenHelper::GenTableCommentString($table),
            '{{TableFillableString}}' => GenHelper::GenTableFillableString($columns),
            '{{ModelRelations}}' => GenHelper::GenTableRelations($table),
        ], $stubContent);
        $filePath = $this->laravel['path'] . '/Models/Base/Base' . $modelName . '.php';
        $result = StubHelper::Save($filePath, $stubContent, $options['force']);
        $this->line($result);
        // Model
        $stubContent = StubHelper::GetStub('Model.stub');
        $stubContent = StubHelper::Replace([
            '{{ModelName}}' => $modelName,
        ], $stubContent);
        $filePath = $this->laravel['path'] . '/Models/' . $modelName . '.php';
        $result = StubHelper::Save($filePath, $stubContent);
        $this->line($result);
    }

    /**
     * @param Table|null $table
     * @param array|null $columns
     * @param string $modelName
     * @param array $prefix
     * @param array $options
     * @throws Exception
     */
    private function makeController(?Table $table, ?array $columns, string $modelName, array $prefix, array $options)
    {

        if (count($prefix) == 0)
            throw new Exception('生成Controller需要模块名称，比如： admin/wechat');

        $rules = $options['rules'] ? 'Rules' : '';
        $hasSoftDelete = TableHelper::GetColumnsHasSoftDelete($table ? $table->getColumns() : []);
        $stubName = $hasSoftDelete ? "controllerWithSoftDelete$rules.stub" : "controller$rules.stub";

        $stubContent = StubHelper::GetStub($stubName);
        $stubContent = StubHelper::Replace([
            '{{ModelName}}' => $modelName,
            '{{TableComment}}' => $table ? $table->getComment() : '',
            '{{ModuleName}}' => implode('\\', $prefix),
            '{{InsertString}}' => $rules ? "{$modelName}Validate" : GenHelper::GenColumnsRequestValidateString($columns, "\t\t\t"),
        ], $stubContent);

        $moduleName = implode('/', $prefix);
        $filePath = $this->laravel['path'] . "/Modules/$moduleName/{$modelName}Controller.php";
        $result = StubHelper::Save($filePath, $stubContent, $options['force']);
        $this->line($result);

        if ($options['rules'])
            $this->makeValidate($modelName, $prefix, $table, $columns, $options);

    }

    /**
     * @param string $modelName
     * @param array $prefix
     * @param $table
     * @param $columns
     * @param $options
     * @return void
     */
    private function makeValidate(string $modelName, array $prefix, $table, $columns, $options): void
    {
        $stubContent = StubHelper::GetStub('validate.stub');
        $stubContent = StubHelper::Replace([
            '{{ModelName}}' => $modelName,
            '{{TableComment}}' => $table ? $table->getComment() : '',
            '{{ModuleName}}' => implode('\\', $prefix),
            '{{InsertString}}' => GenHelper::GenColumnsRequestValidateString($columns, "\t\t\t"),
        ], $stubContent);
        $filePath = $this->laravel['path'] . "/Validates/{$modelName}Validate.php";
        $result = StubHelper::Save($filePath, $stubContent, $options['force']);
        $this->line($result);
    }

    /**
     * @param string $modelName
     * @param array $prefix
     * @param array $options
     * @throws ReflectionException
     * @throws Exception
     */
    private function makeTest(string $modelName, array $prefix, array $options)
    {
        if (count($prefix) == 0)
            throw new Exception('生成Controller需要模块名称，比如： admin/wechat');

        $nameSpace = 'App\\Modules\\' . implode('\\', $prefix) . '\\' . $modelName . 'Controller';
        $controllerFilePath = $this->laravel['path'] . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . implode('/', $prefix) . '/' . $modelName . 'Controller.php';
        $content = GenHelper::GenTestContent($nameSpace, $controllerFilePath);

        $stubContent = StubHelper::GetStub('test.stub');
        $stubContent = StubHelper::Replace([
            '{{controllerIntro}}' => ReflectHelper::GetControllerAnnotation($nameSpace),
            '{{ModelName}}' => $modelName,
            '{{ModuleName}}' => implode('\\', $prefix),
            '{{content}}' => $content,
        ], $stubContent);
        $moduleName = implode('/', $prefix);
        $filePath = $this->laravel['path'] . "/../tests/Modules/$moduleName/{$modelName}ControllerTest.php";
        $result = StubHelper::Save($filePath, $stubContent, $options['force']);
        $this->line($result);
    }

}