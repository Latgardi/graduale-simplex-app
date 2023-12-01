<?php

namespace App\Database\Type;

class PrimaryLiturgicalDayResult
{
    public function __construct(
        public string $title,
        public \PDOStatement $pdo,
        public bool $hasMultipleMasses
    ) {}
}