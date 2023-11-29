<?php

namespace App\Controllers;

use App\HTTPRouter\Dispatcher;
use App\Lib\Enum\ImageType;
use App\Lib\Enum\LiturgyParts;
use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
use App\Localization\LocalizedName;
use App\View\Title;
use DirectoryIterator;
use GradualeSimplex\LiturgicalCalendar\Enum\AliasDominica;
use GradualeSimplex\LiturgicalCalendar\Enum\Season;
use GradualeSimplex\LiturgicalCalendar\Utility\IntToRoman;
use GradualeSimplex\LiturgicalCalendar\Utility\RomanNumber;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

class SeasonController extends BaseController
{
    private const string SEASONS_URL_PREFIX = '/chants/seasons/';

    public function seasons(): void
    {
        Title::set(LocalizedName::for('liturgical year seasons'));
        $result = new ControllerResult();
        $seasons = [];
        foreach (Season::cases() as $season) {
            $seasons[] = [
                'link' => self::SEASONS_URL_PREFIX . strtolower($season->name),
                'title' => LocalizedName::for(strtolower($season->name)),
                'colour' => Season::getColour(season: $season)
            ];
        }
        $result->set('links', $seasons);
        $this->render('link_list', $result);
    }

    public function season($season): void
    {
        $season = Season::tryFromString($season);
        if (is_null($season)) {
            Dispatcher::return404();
        }
        $result = new ControllerResult();
        $links = [];
        if (is_dir($this->getSeasonDir(forSeason: $season))) {
            foreach (new DirectoryIterator(
                         directory: $this->getSeasonDir(forSeason: $season)
                     ) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                $isDominica = AliasDominica::tryNamed($season->name . '_' . $fileInfo->getFilename()) === null;
                $number = new RomanNumber((int) $fileInfo->getFilename());
                $title = $number->getRomanValue() . ' '
                    . ($isDominica ? 'нядзеля' : 'тыдзень');

                $links[] = [
                    'link' => self::SEASONS_URL_PREFIX . strtolower($season->name) . '/' . $fileInfo->getFilename(),
                    'title' => $title,
                    'number' => $number->getIntValue()
                ];
            }
        }
        usort($links, static function ($a, $b) {
            return $a['number'] <=> $b['number'];
        });
        $result->set('links', $links);
        Title::set(LocalizedName::for($season->name) ?? "");
        $this->render('link_list', $result);
    }

    public function day($season, $week, $setTitle = true): void
    {
        $season = Season::tryFromString($season);
        $result = new ControllerResult();
        $relativeDirName = '/library/seasons/' . strtolower($season->name) . '/' . $week;
        $absoluteDirName = $_SERVER['DOCUMENT_ROOT'] . $relativeDirName;
        if (!is_dir($absoluteDirName)) {
            Dispatcher::return404();
        }
        if ($setTitle) {
            $isDominica = AliasDominica::tryNamed($season->name . '_' . $week) === null;
            $number = new RomanNumber((int) $week);
            $title = $number->getRomanValue()
                . ' ' . ($isDominica ? 'нядзеля' : 'тыдзень') . ' '
                . LocalizedName::for('of_' . $season->name);
            $localizedRankName = LocalizedName::for($isDominica ? "dominica" : "feria");
            Title::set($title, $localizedRankName);
            $result->set('title', $title);
        }
        $parts = $this->getLiturgicalParts(absolutePath: $absoluteDirName, relativePath: $relativeDirName);
        $result->set('parts', $parts);
        $this->render('liturgical_day', $result);
    }

    private function getSeasonDir(Season $forSeason): string
    {
        return dirname(__DIR__, 2) . '/library/seasons/' . strtolower($forSeason->name);
    }
}
