<?php

namespace Nichozuo\LaravelFast\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PDOException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionRender
{
    public static function Render(Throwable $e): JsonResponse
    {
        $request = request();
        $class = get_class($e);
        $isDebug = config('app.debug');
        $type = 2;

        $requestInfo = [
            'client' => $request->getClientIps(),
            'method' => $request->getMethod(),
            'uri' => $request->getPathInfo(),
            'params' => $request->all(),
        ];

        $skipLog = self::GetSkipLog($requestInfo['uri']);

        $exceptionInfo = [
            'class' => $class,
            'trace' => self::getTrace($e)
        ];

        switch ($class) {
            case Err::class:
                $code = $e->getCode();
                $message = $e->getMessage();
                $type = $e->getType();
                $description = $e->getDescription();
                break;
            case AuthenticationException::class:
                $arr = Err::AuthUserNotLogin;
                $code = $arr[0];
                $message = $arr[1];
                $description = $arr[2];
                break;
            case ValidationException::class:
                $code = 999;
                $message = "数据验证失败";
                $errMsg = self::getValidationErrors($e->errors());
                $description = "【{$errMsg}】字段验证失败";
                break;
            case NotFoundHttpException::class:
                $code = 999;
                $message = "请求的资源未找到";
                $description = $e->getMessage();
                break;
            case MethodNotAllowedHttpException::class:
                $code = 999;
                $message = "请求方式不正确";
                $description = $e->getMessage();
                break;
            case PDOException::class:
                $code = 999;
                $message = "数据库链接错误";
                $description = $e->getMessage();
                break;
            default:
                $code = 9;
                $message = '系统错误';
                $description = '请联系管理员查看日志';
                break;
        }

        if(!$skipLog)
            Log::error($message, [
                'message' => $description,
                'debug' => [
                    'message' => $e->getMessage(),
                    'request' => $requestInfo,
                    'exception' => $exceptionInfo
                ]
            ]);

        return response()->json([
            'code' => $code,
            'type' => (int)$type,
            'message' => $message,
            'description' => $description,
            'debug' => $isDebug ? [
                'message' => $e->getMessage(),
                'request' => $requestInfo,
                'exception' => $exceptionInfo
            ] : null
        ]);
    }

    /**
     * @param Throwable $e
     * @return array
     */
    private static function getTrace(Throwable $e): array
    {
        $arr = $e->getTrace();
        $file = array_column($arr, 'file');
        $line = array_column($arr, 'line');
        $trace = [];
        for ($i = 0; $i < count($file); $i++) {
            if (!strpos($file[$i], '/vendor/'))
                $trace[] = [
                    $i => "$file[$i]($line[$i])"
                ];
        }
        return $trace;
    }

    /**
     * @param $errors
     * @return string
     */
    private static function getValidationErrors($errors): string
    {
        $err = [];
        foreach ($errors as $key => $value)
            $err[] = $key;
        return implode(',', $err);
    }

    /**
     * @param string $pathInfo
     * @return bool
     */
    private static function GetSkipLog(string $pathInfo): bool
    {
        $skipLogPathInfo = config('common.skipLogPathInfo');
        if (!$skipLogPathInfo)
            return false;

        return in_array($pathInfo, $skipLogPathInfo);
    }
}
