<?php

namespace App\Controllers;

use App\Database\Connector;
use App\Database\QueryBuilder;
use App\HTTPRouter\Dispatcher;
use App\Lib\Enum\ImageType;
use App\Lib\Enum\LiturgyPart;
use App\Lib\Type\ListLink;
use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
use App\Localization\LocalizedCelebration;
use App\Localization\LocalizedName;
use App\View\Title;
use DirectoryIterator;
use GradualeSimplex\LiturgicalCalendar\Enum\AliasDominica;
use GradualeSimplex\LiturgicalCalendar\Enum\CelebrationRank;
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
            $seasons[] = new ListLink(
                title: LocalizedName::for(strtolower($season->name)),
                link: self::SEASONS_URL_PREFIX . strtolower($season->name)
            );
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
        $baseLink = self::SEASONS_URL_PREFIX . strtolower($season->name) . '/';
        $result = new ControllerResult();
        $queryBuilder = new QueryBuilder();
        $days = $queryBuilder->getSeasonDays($season);
        $links = [];
        if (!is_null($days)) {
            foreach ($days as $day) {
                $localizedCelebration = new LocalizedCelebration($day['title']);
                $dayNumber = RomanNumber::excludeFromString($day['title']);
                $links[] = new ListLink(
                    title: $localizedCelebration->getTranslation() ?? $day['title'],
                    link: $baseLink . $day['slug'],
                    number: $dayNumber?->getIntValue()
                );
            }
            usort($links, static function ($a, $b) {
                return $a->number <=> $b->number;
            });
        }
        $result->set('links', $links);
        Title::set(LocalizedName::for($season->name) ?? "");
        $this->render('link_list', $result);
    }

    public function day($season, $slug, $setTitle = true): void
    {
        $season = Season::tryFromString($season);
        $queryBuilder = new QueryBuilder();
        $pdo = $queryBuilder->getSeasonDay($season, $slug);
        $this->renderLiturgicalDay(dayPDO: $pdo, setTitle: $setTitle);
    }
}
