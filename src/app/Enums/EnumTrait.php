<?php

namespace Nichozuo\LaravelFast\Enums;

trait EnumTrait
{
    /**
     * 获得枚举值的数组
     * @return array
     */
    public static function columns(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * 获得枚举值的字符串，逗号分割
     * @param string $prefix
     * @return string
     */
    public static function comment(string $prefix): string
    {
        return $prefix . ':' . implode(",", array_column(self::cases(), 'value'));
    }
}
