# docs
- 启动项目后，通过链接进入文档系统```http://127.0.0.1:8000/docs/index.html```

# 文档生成
- 文件夹的文档名称：在common.php中配置
```php
'docs' => [
    'foldersSubTitleConfig' => [
        'Admin' => '管理员模块',
    ]
]
```
- 控制器的名称：在controller文件中配置，@intro参数
```php
/**
 * @intro 活动
 * Class ActivitiesController
 * @package App\Modules\Admin
 */
class ActivitiesController extends AdminBaseController{

}
```
- 方法的名称：在method中配置，@intro参数
```php
/**
 * @intro 列表
 * @param Request $request
 * @return mixed
 * @throws Err
 */
public function list(Request $request): mixed
{
}
```

- 方法的参数，在$params中配置
  - '#'号后面是说明 
```php
/**
 * @intro 添加
 * @param Request $request
 * @return array
 */
public function store(Request $request): array
{
    $params = $request->validate([
        'name' => 'required|string', # 活动名称
        'start_at' => 'required|date', # 开始时间
        'end_at' => 'required|date', # 结束时间
        'location_limit' => 'required|string', # 地区限制名字
        'daily_limit' => 'required|integer', # 每日总限制次数
        'daily_person_limit' => 'required|integer', # 每人每日限制次数
        'red_pack_json' => 'required|json', # 红包配置
    ]);
    Activities::unique($params, ['name'], '名称');
    Activities::create($params);
    return [];
}
```
- 方法的返回值：在方法的php-doc里写
```php
 * @responseParams name,string,说明
```
