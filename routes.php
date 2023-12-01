<?php

use App\Controllers\AboutController;
use App\Controllers\CalendarController;
use App\Controllers\ChantController;
use App\Controllers\FormulasController;
use App\Controllers\PrimaryLiturgicalDaysController;
use App\Controllers\RankController;
use App\Controllers\MainController;
use App\Controllers\SeasonController;
use App\HTTPRouter\Route;
use GradualeSimplex\LiturgicalCalendar\Enum\CelebrationRank;

Route::get(
    route: '/calendar/$year/$month',
    action: [new CalendarController(), 'calendar']
);
Route::get(
    route: '/calendar/$year',
    action: [new CalendarController(), 'year']
);
Route::get(
    route: '/calendar',
    action: [new CalendarController(), 'calendarMain']
);
Route::get(
    route: '/chants/primary-liturgical-days/$daySlug/$massSlug',
    action: [new PrimaryLiturgicalDaysController(), 'mass']
);
Route::get(
    route: '/chants/primary-liturgical-days/$slug',
    action: [new PrimaryLiturgicalDaysController(), 'day']
);
Route::get(
    route: '/chants/primary-liturgical-days',
    action: [new PrimaryLiturgicalDaysController(), 'days']
);
Route::get(
    route: '/chants/seasons/$season/$slug',
    action: [new SeasonController(), 'day']
);
Route::get(
    route: '/chants/seasons/$season',
    action: [new SeasonController(), 'season']
);
Route::get(
    route: '/chants/seasons',
    action: [new SeasonController(), 'seasons']
);
Route::get(
    route: '/chants/formulas',
    action: [new FormulasController(), 'formulas']
);
Route::get(
    route: '/chants/solemnities/$name',
    action: [new RankController(rank: CelebrationRank::Sollemnitas), 'celebration']
);
Route::get(
    route: '/chants/solemnities',
    action: [new RankController(rank: CelebrationRank::Sollemnitas), 'celebrations']
);
Route::get(
    route: '/chants/feasts/$name',
    action: [new RankController(rank: CelebrationRank::Festum), 'celebration']
);
Route::get(
    route: '/chants/feasts',
    action: [new RankController(rank: CelebrationRank::Festum), 'celebrations']
);
Route::get(
    route: '/chants/memorials/$name',
    action: [new RankController(rank: CelebrationRank::Memoria), 'celebration']
);
Route::get(
    route: '/chants/memorials',
    action: [new RankController(rank: CelebrationRank::Memoria), 'celebrations']
);
Route::get(
    route: '/chants',
    action: [new ChantController(), 'categoryList']
);
Route::get(
    route: '/about',
    action: [new AboutController(), 'index']
);
Route::get(
    route: '/',
    action: [new MainController(), 'main']
);
