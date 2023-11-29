<?php

namespace App\Lib\Enum;

enum ChantCategory
{
    case Triduum_Paschale;
    case Primary_Liturgical_Days;
    case Solemnities;
    case Feasts;
    case Memorials;
    case Seasons;
    case Ritual_Masses;
    case Other;
    case Formulas;

    // TODO dev solution, need to remove
    public function getStatus(): CategoryWorkingStatus
    {
        return match ($this) {
            self::Feasts => CategoryWorkingStatus::InWork,
            self::Formulas => CategoryWorkingStatus::InWork,
            self::Other => CategoryWorkingStatus::IsEmptyYet,
            self::Memorials => CategoryWorkingStatus::InWork,
            self::Ritual_Masses => CategoryWorkingStatus::IsEmptyYet,
            self::Seasons => CategoryWorkingStatus::InWork,
            self::Primary_Liturgical_Days => CategoryWorkingStatus::InWork,
            self::Solemnities => CategoryWorkingStatus::InWork,
            self::Triduum_Paschale => CategoryWorkingStatus::IsEmptyYet,
        };
    }
}
