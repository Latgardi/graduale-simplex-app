<?php

namespace GradualeSimplex\LiturgicalCalendar\Enum;
use GradualeSimplex\LiturgicalCalendar\Interface\EnumFromString;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

enum LiturgicalColour: string implements EnumFromString
{
    // Backed value is css colour (maybe hex)
    case Green = 'green';
    case White = 'white';
    case Red = 'red';
    case Pink = 'pink';
    case Black = 'black';
    case Violet = 'violet';

    public static function tryFromString(string $string): ?self
    {
        return match ($string) {
            "green" => self::Green,
            "white" => self::White,
            "red" => self::Red,
            "pink" => self::Pink,
            "black" => self::Black,
            "violet" => self::Violet,
            default => null
        };
    }
}
