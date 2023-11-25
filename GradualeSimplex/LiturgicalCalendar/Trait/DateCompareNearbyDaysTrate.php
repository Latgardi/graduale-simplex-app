<?php

namespace GradualeSimplex\LiturgicalCalendar\Trait;

use GradualeSimplex\LiturgicalCalendar\Enum\RelativeDay;

trait DateCompareNearbyDaysTrate
{
    public function isNearbyDay(): RelativeDay
    {
        $now = new self();
        $now->setTime( 0, 0, 0 );
        $this->setTime( 0, 0, 0 );

        var_dump($now->diff($otherDate)->days === 0); // Today
        var_dump($now->diff($otherDate)->days === -1); // Yesterday
        var_dump($now->diff($otherDate)->days === 1);
    }
}
