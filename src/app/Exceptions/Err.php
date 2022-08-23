<?php

namespace Nichozuo\LaravelFast\Exceptions;

use Exception;

class Err extends Exception
{
    const AuthUserNotLogin = ['message' => '未登录', 'code' => 10000];
    const AuthUserPasswordWrong = ['message' => '账号密码错误', 'code' => 10001];

    /**
     * @param string $message
     * @param int $code
     * @return mixed
     * @throws Err
     */
    public static function NewText(string $message, int $code = 9999): mixed
    {
        throw new static($message, $code);
    }
}
