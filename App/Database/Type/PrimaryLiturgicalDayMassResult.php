<?php

namespace App\Database\Type;

use GradualeSimplex\LiturgicalCalendar\Enum\CelebrationRank;

class PrimaryLiturgicalDayMassResult
{
    public function __construct(
        public \PDOStatement $pdo,
        public string $massTitle,
        public string $dayTitle,
        public ?CelebrationRank $dayRank
    ) {}
}