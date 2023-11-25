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
}