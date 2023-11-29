<?php

namespace GradualeSimplex\LiturgicalCalendar\Trait;
use GradualeSimplex\LiturgicalCalendar\Exception\EnumCaseNotExistException;

require dirname(__DIR__, 3) . '/vendor/autoload.php';
trait NamedEnumGetterTrait
{
    public static function named($name)
    {
        $constantName = self::class . '::' . $name;
        if (defined($constantName)) {
            $enum = constant($constantName);
            return $enum->value ?? $enum;
        }

        throw new EnumCaseNotExistException(message: 'Case does not exist');
    }

    public static function tryNamed($name)
    {
        $constantName = self::class . '::' . $name;
        if (defined($constantName)) {
            $enum = constant($constantName);
            return $enum->value ?? $enum;
        }

        return null;
    }
}
