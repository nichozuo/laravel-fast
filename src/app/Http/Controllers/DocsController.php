<?php

namespace Nichozuo\LaravelFast\Http\Controllers;

use Doctrine\DBAL\Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Nichozuo\LaravelFast\Helpers\DbalHelper;
use Nichozuo\LaravelFast\Helpers\DocsHelper;
use ReflectionException;

class DocsController extends BaseController
{
    use ControllerTrait;

    private string $basePath;

    /**
     * HomeController constructor.
     * @throws Exception
     */
    public function __construct()
    {
        DbalHelper::register();
    }

    /**
     * @param Request $request
     * @return array
     * @throws ReflectionException
     */
    public function getMenu(Request $request): array
    {
        $params = $request->validate([
            'type' => 'required|string',
        ]);
        return match ($params['type']) {
            'readme' => DocsHelper::GetReadmeMenu(),
            'modules' => DocsHelper::GetModulesMenu(app_path('Modules' . DIRECTORY_SEPARATOR)),
            'database' => DocsHelper::GetDatabaseMenu(),
            default => [],
        };
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function getContent(Request $request): array
    {
        $params = $request->validate([
            'type' => 'required|string',
            'key' => 'required|string',
        ]);
        return match ($params['type']) {
            'readme' => DocsHelper::GetReadmeContent($params['key']),
            'modules' => DocsHelper::GetModulesContent($params['key']),
            'database' => DocsHelper::GetDatabaseContent($params['key']),
            default => [],
        };
    }
}