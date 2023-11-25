<?php

namespace App;

class Config
{
    private static array $keys;
    public static function set($key, $value): void
    {
        self::$keys[$key] = $value;
    }

    public static function get($key): mixed
    {
        return self::$keys[$key];
    }
}