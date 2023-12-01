<?php

namespace GradualeSimplex\LiturgicalCalendar\Enum;
use GradualeSimplex\LiturgicalCalendar\Interface\EnumFromString;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
enum CelebrationRank: string implements EnumFromString
{
    case Feria = "Feria";
    case MemoriaAdLibitum = "Memoria ad libitum";
    case Memoria = "Memoria";
    case Dominica = "Dominica";
    case Sollemnitas = "Sollemnitas";
    case Festum = "Festum";
    case DiesLiturgiciPrimarii = "Dies liturgici primarii";
    case TriduumPaschale = "Triduum Paschale";
    case Commemoratio = "Commemoratio";

    public static function tryFromString(string $string): ?self
    {
        return match ($string) {
            "dies liturgici primarii" => self::DiesLiturgiciPrimarii,
            "feria" => self::Feria,
            "memoria" => self::Memoria,
            "sollemnitas" => self::Sollemnitas,
            "festum" => self::Festum,
            "dominica" => self::Dominica,
            "memoria ad libitum" => self::MemoriaAdLibitum,
            "triduum paschale" => self::TriduumPaschale
        };
    }
}
