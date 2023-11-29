<?php

namespace App;

use App\Lib\Exception\ConfigException;

class Config
{
    private static array $keys;
    public static function set($key, $value): void
    {
        self::$keys[$key] = $value;
    }

    public static function setMultiple(array $dictionary): void
    {
        if (array_is_list($dictionary)) {
            throw new ConfigException('Method excepts associative array.');
        }
        self::$keys = array_merge(self::$keys, $dictionary);
    }

    public static function get($key): mixed
    {
        return self::$keys[$key] ?? null;
    }
}