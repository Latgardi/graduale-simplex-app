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

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

class SeasonController extends BaseController
{
    private const SEASONS_URL_PREFIX = '/chants/seasons/';

    public static function seasons(): void
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
        self::render('link_list', $result);
    }

    public static function season($season): void
    {
        $season = Season::tryFromString($season);
        if (is_null($season)) {
            Dispatcher::return404();
        }
        $result = new ControllerResult();
        $links = [];
        if (is_dir(self::getSeasonDir(forSeason: $season))) {
            foreach (new DirectoryIterator(
                         directory: self::getSeasonDir(forSeason: $season)
                     ) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                $isDominica = AliasDominica::tryNamed($season->name . '_' . $fileInfo->getFilename()) === null;
                $title = IntToRoman::convert((int)$fileInfo->getFilename()) . ' '
                    . ($isDominica ? 'нядзеля' : 'тыдзень');

                $links[] = [
                    'link' => self::SEASONS_URL_PREFIX . strtolower($season->name) . '/' . $fileInfo->getFilename(),
                    'title' => $title
                ];
            }
        }
        $result->set('links', $links);
        Title::set(LocalizedName::for($season->name) ?? "");
        self::render('link_list', $result);
    }

    public static function day($season, $week, $setTitle = true): void
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
            $title = IntToRoman::convert((int)$week)
                . ' ' . ($isDominica ? 'нядзеля' : 'тыдзень') . ' '
                . LocalizedName::for('of_' . $season->name);
            $localizedRankName = LocalizedName::for($isDominica ? "dominica" : "feria");
            Title::set($title, $localizedRankName);
            $result->set('title', $title);
        }
        $parts = self::getLiturgicalParts(absolutePath: $absoluteDirName, relativePath: $relativeDirName);
        $result->set('parts', $parts);
        self::render('liturgical_day', $result);
    }

    private static function getSeasonDir(Season $forSeason): string
    {
        return dirname(__DIR__, 2) . '/library/seasons/' . strtolower($forSeason->name);
    }
}
