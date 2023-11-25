<?php

namespace GradualeSimplex\LiturgicalCalendar\Enum;
use GradualeSimplex\LiturgicalCalendar\Interface\EnumFromString;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
enum Weekday implements EnumFromString
{
    case Monday;
    case Tuesday;
    case Wednesday;
    case Thursday;
    case Friday;
    case Saturday;
    case Sunday;

    public static function tryFromString(string $string): ?self
    {
        return match ($string) {
            "monday" => self::Monday,
            "tuesday" => self::Tuesday,
            "wednesday" => self::Wednesday,
            "thursday" => self::Thursday,
            "friday" => self::Friday,
            "saturday" => self::Saturday,
            "sunday" => self::Sunday,
            default => null
        };
    }
}
