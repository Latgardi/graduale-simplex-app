<?php

namespace App\Controllers;

use App\Lib\Type\ControllerResult;
use App\Localization\LocalizedLiturgicalCalendar;
use App\Localization\LocalizedName;
use App\View\Title;
use GradualeSimplex\LiturgicalCalendar\Enum\Language;
use GradualeSimplex\LiturgicalCalendar\Enum\Month;
use GradualeSimplex\LiturgicalCalendar\LiturgicalCalendar;

class CalendarController extends BaseController
{
    public static function calendarMain(): void
    {
        Title::set('Каляндар');
        $result = new ControllerResult();
        $result->set('title', LocalizedName::for(date('F')) . ' ' . date('Y'));
        $date = new \DateTime();
        $date->setTimestamp(time());
        self::calendar(
            year: $date->format("Y"),
            month: $date->format("m"),
            result: $result
        );
    }

    public static function calendar($year, $month, ?ControllerResult $result = null) :void
    {
        $language = !isset($_GET['lang']) || $_GET['lang'] === 'be' ? Language::Belarusian : Language::Latin;
        $selectedMonth = $month;
        $result = $result ?? new ControllerResult();
        if (!$result->isSet('title')) {
            $title = LocalizedName::for(Month::tryFrom((int) $month)->name);
            $result->set('title', $title);
            Title::set($title);
        }
        $months = [];
        foreach (Month::cases() as $case) {
            $months[] = [
                'title' => LocalizedName::for($case->name),
                'link' => '/calendar/' . $year . '/' . $case->value . '/',
                'current' => (int) $selectedMonth === $case->value
            ];
        }
        $years = [];
        foreach (range(1970, 2030) as $yearNumber) {
            $years[] = [
                'title' => $yearNumber,
                'link' => '/calendar/' . $yearNumber . '/',
                'current' => (int) $year === $yearNumber
            ];
        }
        $result->set('months', $months);
        $result->set('currentMonth', $selectedMonth);
        $result->set('years', $years);
        $liturgicalCalendar = new LiturgicalCalendar();
        $calendar = $liturgicalCalendar->getCalendar(year: $year, month: $month);
        if ($language === Language::Belarusian) {
            $localizedCalendar = new LocalizedLiturgicalCalendar($calendar);
            $calendar = $localizedCalendar->getCalendar();
        }
        $result->set('language', $language);
        $result->set('calendar', $calendar);
        self::render('calendar', $result);
    }

    public static function year($year): void
    {
        Title::set(LocalizedName::for('year'));
        $result = new ControllerResult();
        $months = [];
        foreach (Month::cases() as $month) {
            $months[] = [
                'title' => LocalizedName::for($month->name) ?? $month->name,
                'link' => $month->value . '/'
            ];
        }
        $result->set('links', $months);
        self::render('link_list', $result);
    }
}
