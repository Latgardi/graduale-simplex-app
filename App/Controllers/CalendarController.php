<?php

namespace App\Controllers;

use App\Lib\Type\ControllerResult;
use App\Localization\LocalizedLiturgicalCalendar;
use App\Localization\LocalizedName;
use App\View\Title;
use GradualeSimplex\LiturgicalCalendar\Enum\Language;
use GradualeSimplex\LiturgicalCalendar\Enum\Month;
use GradualeSimplex\LiturgicalCalendar\LiturgicalCalendar;
use GradualeSimplex\LiturgicalCalendar\Type\DateTimeExt;

class CalendarController extends BaseController
{
    public function calendarMain(): void
    {
        Title::set('Каляндар');
        $result = new ControllerResult();
        $result->set('title', LocalizedName::for(date('F')) . ' ' . date('Y'));
        $date = new DateTimeExt();
        $date->setTimestamp(time());
        $this->calendar(
            year: $date->format("Y"),
            month: $date->format("m"),
            result: $result,
            isCurrent: true
        );
    }

    public function calendar($year, $month, ?ControllerResult $result = null, bool $isCurrent = false) :void
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
                'link' => '/calendar/' . $year . '/' . $case->value,
                'current' => (int) $selectedMonth === $case->value
            ];
        }
        $years = [];
        foreach (range(1970, 2030) as $yearNumber) {
            $years[] = [
                'title' => $yearNumber,
                'link' => '/calendar/' . $yearNumber,
                'current' => (int) $year === $yearNumber
            ];
        }
        $nearbyMonths = $this->getNearbyMonthLinks($selectedMonth, $year);

        $result->set('prevMonth', $nearbyMonths['prevMonth']);
        $result->set('nextMonth', $nearbyMonths['nextMonth']);
        $result->set('months', $months);
        $result->set('currentMonth', $selectedMonth);
        $result->set('isCurrentMonth', $isCurrent);
        $result->set('years', $years);
        $liturgicalCalendar = new LiturgicalCalendar();
        $calendar = $liturgicalCalendar->getCalendar(year: $year, month: $month);
        if ($language === Language::Belarusian) {
            $localizedCalendar = new LocalizedLiturgicalCalendar($calendar);
            $calendar = $localizedCalendar->getCalendar();
        }
        $result->set('language', $language);
        $result->set('calendar', $calendar);
        $this->render('calendar', $result);
    }

    public function year($year): void
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
        $this->render('link_list', $result);
    }

    private function getNearbyMonthLinks(int $month, int $year): array
    {
        $nextMonthYear = $month === 12 ? $year + 1 : $year;
        $nextMonthValue = $month === 12 ? 1 : $month + 1;
        $nextMonthLink = "/calendar/$nextMonthYear/$nextMonthValue";

        $prevMonthYear = $month === 1 ? $year - 1 : $year;
        $prevMonthValue = $month === 1 ? 12 : $month - 1;
        $prevMonthLink = "/calendar/$prevMonthYear/$prevMonthValue";

        $prevMonth = [
            'title' => LocalizedName::for(Month::tryFrom($prevMonthValue)->name)
                . " $prevMonthYear",
            'link' => $prevMonthLink
        ];

        $nextMonth = [
            'title' => LocalizedName::for(Month::tryFrom($nextMonthValue)->name)
                . " $nextMonthYear",
            'link' => $nextMonthLink
        ];

        return [
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
        ];
    }
}
