<?php

namespace GradualeSimplex\LiturgicalCalendar\Type;
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

use GradualeSimplex\LiturgicalCalendar\Enum\Season;
use GradualeSimplex\LiturgicalCalendar\Enum\Weekday;
use GradualeSimplex\LiturgicalCalendar\Exception\ZeroCelebrationException;


class LiturgicalDay
{
    private const WEEK_REG_EXP = "#.+\s([XIV]+)\s(.+)#";
    /**
     * @param DateTimeExt $date
     * @param Season $season
     * @param int $seasonWeek
     * @param array<Celebration> $celebrations
     * @param Weekday $weekday
     * @throws ZeroCelebrationException
     */
    public function __construct(
        public readonly DateTimeExt $date,
        public readonly Season $season,
        public readonly int $seasonWeek,
        public array $celebrations,
        public readonly Weekday $weekday
    ) {
        if (count($this->celebrations) === 0) {
            throw new ZeroCelebrationException();
        }
    }

    public function hasOneCelebration(): bool
    {
        return count($this->celebrations) === 1;
    }

    public function isFeast(): bool
    {
        return preg_match(self::WEEK_REG_EXP, $this->celebrations[0]->title);
    }

    public function getMainCelebration(): Celebration
    {
        // TODO Implement collection class
        return $this->celebrations[0];
    }
}
