<?php

namespace App\Lib\Type;

use GradualeSimplex\LiturgicalCalendar\Enum\CelebrationRank;

class CelebrationPropers
{
    public function __construct(
        public string $title,
        public CelebrationRank $rank,
        public ?ChantItem $introitus,
        public ?ChantItem $offertorium,
        public ?ChantItem $communio
    ) {}

    /**
     * @return array<ChantItem>
     */
    public function getChants(): array
    {
        $fields = [];

        foreach ([$this->introitus, $this->offertorium, $this->communio] as $property) {
            if (!is_null($property)) {
                $fields[] = $property;
            }
        }
        return $fields;
    }
}