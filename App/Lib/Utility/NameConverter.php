<?php

namespace App\Lib\Utility;

class NameConverter
{
    public static function titleToWeb(string $title): string
    {
        return strtolower(str_replace(' ', '-', $title));
    }

    public static function webToTitle(string $title): string
    {
        return str_replace('-', ' ', $title);
    }
}