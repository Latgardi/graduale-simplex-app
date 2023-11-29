<?php

namespace App\View;

use App\Config;

class Title
{
    private static ?string $title = null;
    private static ?string $subtitle = null;
    public static function set(string $title, ?string $subtitle = null): void
    {
        self::$title = $title;
        self::$subtitle = $subtitle;
    }

    public static function getTitle(): ?string
    {
        return self::$title ?? Config::get('title');
    }

    public static function getSubtitle(): ?string
    {
        return self::$subtitle ?? Config::get('subtitle');
    }
}
