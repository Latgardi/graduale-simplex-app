<?php

namespace App\Controllers;

use App\Config;
use App\View\Title;
use GradualeSimplex\LiturgicalCalendar\Enum\CelebrationRank;
use GradualeSimplex\LiturgicalCalendar\Enum\Language;
use GradualeSimplex\LiturgicalCalendar\LiturgicalCalendar;

class MainController extends BaseController
{
    public function main() {
        $calendar = new LiturgicalCalendar();
        $today = $calendar->getToday(language: Language::Belarusian);
        $mainCelebration = $today?->celebrations[0];
        /*switch ($mainCelebration->rank) {
            case CelebrationRank::DiesLiturgiciPrimarii:
                (new PrimaryLiturgicalDaysController())->day();
        }*/
        Title::set(Config::get('title'));
        $this->render('main');
    }
}
