<?php

namespace GradualeSimplex\LiturgicalCalendar\Enum;
use GradualeSimplex\LiturgicalCalendar\Interface\EnumFromString;
use GradualeSimplex\LiturgicalCalendar\Trait\NamedEnumGetterTrait;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
enum Season: string implements EnumFromString
{
    use NamedEnumGetterTrait;
    case Ordinary = "Per annum";
    case Advent = "Adventus";
    case Christmas = "Nativitatis";
    case Lent = "Quadragesimae";
    case Easter = "Paschali";

    public static function tryFromString(string $string): ?self
    {
        return match (strtolower($string)) {
            "christmas" => self::Christmas,
            "ordinary" => self::Ordinary,
            "advent" => self::Advent,
            "lent" => self::Lent,
            "easter" => self::Easter,
            default => null
        };
    }

    public static function getColour(self $season): LiturgicalColour
    {
        return match ($season) {
            self::Advent, self::Lent => LiturgicalColour::Violet,
            self::Ordinary => LiturgicalColour::Green,
            self::Christmas => LiturgicalColour::White,
            self::Easter => LiturgicalColour::White,
        };
    }
}
