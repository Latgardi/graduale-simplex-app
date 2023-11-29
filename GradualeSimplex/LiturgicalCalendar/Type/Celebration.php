<?php

namespace GradualeSimplex\LiturgicalCalendar\Type;
require dirname(__DIR__, 3) . '/vendor/autoload.php';
use GradualeSimplex\LiturgicalCalendar\Enum\CelebrationRank;
use GradualeSimplex\LiturgicalCalendar\Enum\LiturgicalColour;

class Celebration
{
    public function __construct(
        public string $title,
        public LiturgicalColour $colour,
        public CelebrationRank $rank,
        public ?float $rankNum = null,
        public ?string $chantLink = null,
        public ?string $comment = null
    ) {}
}
