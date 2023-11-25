<?php

namespace GradualeSimplex\LiturgicalCalendar\Type;

use GradualeSimplex\LiturgicalCalendar\Enum\RelativeDay;

class DateTimeExt extends \DateTime
{
    public function isToday(): bool
    {
        return $this->isNearbyDay() === RelativeDay::Today;
    }

    public function isNearbyDay(): ?RelativeDay
    {
        $now = new self();
        $now->setTime( 0, 0, 0 );
        $thisDate = clone $this;
        $thisDate->setTime( 0, 0, 0 );
        $diff = $now->diff($thisDate)->days;
        return match ($diff) {
            0 => RelativeDay::Today,
            -1 => RelativeDay::Yesterday,
            1 => RelativeDay::Tomorrow,
            default => null
        };
    }
}
