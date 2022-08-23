<?php

namespace Nichozuo\LaravelFast\Exceptions;

use Exception;

class Err extends Exception
{
    /**
     * @param array $arr
     * @param string $description
     * @param int $type
     * @return mixed
     * @throws Err
     */
    public static function New(array $arr, string $description = '', int $type = 2): mixed
    {
        if ($description == '' && count($arr) == 3)
            $description = $arr[2];

        throw new static((int)$arr[0], $arr[1], $description, $type);
    }

    /**
     * @param string $message
     * @param string $description
     * @param int $type
     * @return mixed
     * @throws Err
     */
    public static function NewText(string $message, string $description = '', int $type = 2): mixed
    {
        throw new static(999, $message, $description, $type);
    }
}
