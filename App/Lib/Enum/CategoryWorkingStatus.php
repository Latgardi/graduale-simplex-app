<?php

namespace App\Lib\Enum;

use GradualeSimplex\LiturgicalCalendar\Trait\NamedEnumGetterTrait;

// TODO dev solution; remove
enum CategoryWorkingStatus
{
    case Finished;
    case InWork;
    case IsEmptyYet;
}
