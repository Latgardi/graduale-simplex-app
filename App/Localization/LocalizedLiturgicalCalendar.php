<?php

namespace App\Localization;

use GradualeSimplex\LiturgicalCalendar\Type\Celebration;
use GradualeSimplex\LiturgicalCalendar\Type\LiturgicalDay;

class LocalizedLiturgicalCalendar
{
    /**
     * @param array<LiturgicalDay> $days
     */
    public function __construct(
        private array $days
    ) {}

    public function getCalendar(): array
    {
        foreach ($this->days as &$day) {
            $localizedDay = new LocalizedLiturgicalDay(day: $day);
            $day = $localizedDay->getDay();
        }
        return $this->days;
    }
}