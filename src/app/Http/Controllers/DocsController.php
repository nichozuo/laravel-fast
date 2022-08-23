<?php

namespace Nichozuo\LaravelCommon\DevTools\Docs;

use Doctrine\DBAL\Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Nichozuo\LaravelCommon\DevTools\Helpers\DbalHelper;
use Nichozuo\LaravelCommon\DevTools\Helpers\DocsHelper;
use Nichozuo\LaravelCommon\Traits\ControllerTrait;

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
     * @title 获取Api文档的菜单
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function getMenu(Request $request): array
    {
        $params = $request->validate([
            'type' => 'required|string',
        ]);
        switch ($params['type']) {
            case 'readme':
                return DocsHelper::GetReadmeMenu();
            case 'modules':
                return DocsHelper::GetModulesMenu(app_path('Modules' . DIRECTORY_SEPARATOR));
            case 'database':
                return DocsHelper::GetDatabaseMenu();
            default:
                return [];
        }
    }

    /**
     * @intro 获取md的内容
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
        switch ($params['type']) {
            case 'readme':
                return DocsHelper::GetReadmeContent($params['key']);
            case 'modules':
                return DocsHelper::GetModulesContent($params['key']);
            case 'database':
                return DocsHelper::GetDatabaseContent($params['key']);
            default:
                return [];
        }
    }
}