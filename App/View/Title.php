<?php

namespace App\View;

class Title
{
    private static string $title = '';
    private static ?string $subtitle = null;
    public static function set(string $title, ?string $subtitle = null): void
    {
        self::$title = $title;
        self::$subtitle = $subtitle;
    }

    public static function getTitle(): string
    {
        return self::$title;
    }

    public static function getSubtitle(): ?string
    {
        return self::$subtitle;
    }
}
