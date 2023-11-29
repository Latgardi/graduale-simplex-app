<?php

namespace App\Localization;

use GradualeSimplex\LiturgicalCalendar\Type\LiturgicalDay;

class LocalizedLiturgicalDay
{
    public function __construct(
        private LiturgicalDay $day
    ) {}

    public function getDay(): LiturgicalDay
    {
        foreach ($this->day->celebrations as $celebration) {
            $localizedCelebration = new LocalizedCelebration(title: $celebration->title);
            if ($newTitle = $localizedCelebration->getTranslation()) {
                $celebration->title = $newTitle;
            }
        }
        return $this->day;
    }
}