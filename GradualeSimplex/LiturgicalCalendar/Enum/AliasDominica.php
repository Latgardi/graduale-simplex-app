<?php

namespace GradualeSimplex\LiturgicalCalendar\Enum;

use GradualeSimplex\LiturgicalCalendar\Trait\NamedEnumGetterTrait;

enum AliasDominica: string
{
    use NamedEnumGetterTrait;
    case Ordinary_34 = 'Domini nostri Iesu Christi universorum regis';
}
