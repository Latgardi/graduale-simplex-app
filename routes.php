<?php

use App\Controllers\AboutController;
use App\Controllers\CalendarController;
use App\Controllers\ChantController;
use App\Controllers\FormulasController;
use App\Controllers\PrimaryLiturgicalDaysController;
use App\Controllers\SolemnityController;
use App\Controllers\MainController;
use App\Controllers\SeasonController;
use App\HTTPRouter\Route;
Route::get(route: '/calendar/$year/$month', action: [CalendarController::class, 'calendar']);
Route::get(route: '/calendar/$year', action: [CalendarController::class, 'year']);
Route::get(route: '/calendar', action: [CalendarController::class, 'calendarMain']);
Route::get(route: '/chants/primary-liturgical-days/$day/$name', action: [PrimaryLiturgicalDaysController::class, 'mass']);
Route::get(route: '/chants/primary-liturgical-days/$name', action: [PrimaryLiturgicalDaysController::class, 'day']);
Route::get(route: '/chants/primary-liturgical-days', action: [PrimaryLiturgicalDaysController::class, 'days']);
Route::get(route: '/chants/seasons/$season/$week', action: [SeasonController::class, 'day']);
Route::get(route: '/chants/seasons/$season', action: [SeasonController::class, 'season']);
Route::get(route: '/chants/seasons', action: [SeasonController::class, 'seasons']);
Route::get(route: '/chants/formulas', action: [FormulasController::class, 'formulas']);
Route::get(route: '/chants/solemnities/$name', action: [SolemnityController::class, 'solemnity']);
Route::get(route: '/chants/solemnities', action: [SolemnityController::class, 'solemnities']);
Route::get(route: '/chants', action: [ChantController::class, 'categoryList']);
Route::get(route: '/about', action: [AboutController::class, 'index']);
Route::get(route: '/', action: [MainController::class, 'main']);
