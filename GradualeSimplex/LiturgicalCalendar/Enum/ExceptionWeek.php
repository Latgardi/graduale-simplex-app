<?php

namespace GradualeSimplex\LiturgicalCalendar\Enum;

use GradualeSimplex\LiturgicalCalendar\Trait\NamedEnumGetterTrait;

enum ExceptionWeek
{
    use NamedEnumGetterTrait;
    case Advent_1;
    case Advent_2;
    case Advent_3;
    case Advent_4;
}