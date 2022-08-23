# laravel-common 快速开发包

### Install
```bash
composer require nichozuo/laravel-common -vvv
```

### vendor publish
```bash
php artisan vendor:publish --provider="Nichozuo\LaravelCommon\ServiceProvider"
```

### composer mirror
```bash
composer config repo.packagist composer https://mirrors.aliyun.com/composer/
```

### App\Exceptions\Handler.php
```php
// 增加两个方法调用
public function register()
{
    // 停止输出系统自带的错误提示
    $this->reportable(function (Throwable $e) {
        //
    })->stop();
    // 替换成自己的统一错误处理方法
    $this->renderable(function (Throwable $e) {
        return ExceptionRender::Render($e);
    });
}
```

### App\Http\Controllers\Controller.php
```php
// 增加一个trait
use ControllerTrait;
```

### App\Http\Middleware\Authenticate.php
```php
protected function redirectTo($request)
{
// 这里注释掉
//    if (! $request->expectsJson()) {
//        return route('login');
//    }
}
```

### App\Http\Kernel.php
```php
'api' => [
    // ...
    // 增加这里，统一处理返回数据
    JsonResponseMiddleware::class
],
```