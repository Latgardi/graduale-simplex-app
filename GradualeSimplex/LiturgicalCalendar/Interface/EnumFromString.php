<?php

namespace GradualeSimplex\LiturgicalCalendar\Interface;

interface EnumFromString
{
    public static function tryFromString(string $string): ?self;
}
