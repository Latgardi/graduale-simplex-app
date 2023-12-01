<?php

namespace App\Controllers;

use App\Database\QueryBuilder;
use App\HTTPRouter\Dispatcher;
use App\Lib\Enum\ImageType;
use App\Lib\Enum\LiturgyPart;
use App\Lib\Type\CelebrationPropers;
use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
use App\Localization\LocalizedCelebration;
use App\Localization\LocalizedName;
use App\View\Title;
use GradualeSimplex\LiturgicalCalendar\Enum\CelebrationRank;
use GradualeSimplex\LiturgicalCalendar\Utility\SolemnitySlug;

class PrimaryLiturgicalDaysController extends BaseController
{
    private const URL_PREFIX = '/chants/primary-liturgical-days/';
    private const CelebrationRank RANK = CelebrationRank::DiesLiturgiciPrimarii;
    public function days(): void
    {
        Title::set(LocalizedName::for('primary liturgical days'));
        $queryBuilder = new QueryBuilder();
        $days = $queryBuilder->getCelebrationsOfRank(rank: self::RANK);
        $this->renderCelebrationList(celebrations: $days, URLPrefix: self::URL_PREFIX);
    }

    public function day(string $slug, bool $setTitle = true): void
    {
        $queryBuilder = new QueryBuilder();
        $day = $queryBuilder->getPrimaryLiturgicalDay(slug: $slug);
        if (is_null($day)) {
            Dispatcher::return404();
        }
        if ($day->hasMultipleMasses) {
            $localizedCelebration = new LocalizedCelebration($day->title);
            Title::set(
                title: $localizedCelebration->getTranslation(),
                subtitle: LocalizedName::for(self::RANK->value)
            );
            $this->renderCelebrationList(celebrations: $day?->pdo, URLPrefix: self::URL_PREFIX . $slug . '/');
        } else {
            $this->renderLiturgicalDay(dayPDO: $day->pdo, setTitle: $setTitle);
        }
    }

    public function mass(string $daySlug, string $massSlug): void
    {
        $queryBuilder = new QueryBuilder();
        $queryResult = $queryBuilder->getPrimaryLiturgicalDayMass(daySlug: $daySlug, massSlug: $massSlug);
        if (!is_null($queryResult)) {
            $localizedCelebration = new LocalizedCelebration($queryResult->dayTitle);
            $title = $localizedCelebration->getTranslation() . ' — ' . $queryResult->massTitle;
            $propers = $this->getMassPropers(
                pdo: $queryResult->pdo,
                title: $title,
                rank: $queryResult->dayRank
            );
            if (is_null($propers)) {
                Dispatcher::return404();
            }
            $result = new ControllerResult();
            $localizedRankName = LocalizedName::for($queryResult->dayRank->value);
            Title::set($title, $localizedRankName);
            $result->set('title', $title);
            $result->set('propers', $propers);
            $this->render('liturgical_day', $result);
        }
    }

    private function getMassPropers(\PDOStatement $pdo, string $title, CelebrationRank $rank): ?CelebrationPropers
    {
        if ($mass = $pdo->fetchAll()) {
            $parts = [];
            foreach ($mass as $chant) {
                $imageType = null;
                $imagePath = null;
                $chantTitle = $chant['title'];
                $part = LiturgyPart::tryFrom($chant['part']);
                if (!is_null($part)) {
                    foreach (ImageType::cases() as $type) {
                        $fileName = $_SERVER['DOCUMENT_ROOT'] . self::SCORES_DIR . $chantTitle
                            . $type->value;
                        if (file_exists(
                            filename: $fileName
                        )) {
                            $imageType = $type;
                            $imagePath = self::SCORES_DIR . $chantTitle
                                . $type->value;
                            break;
                        }
                    }
                    $proper = new ChantItem(
                        title: $chantTitle ?? '',
                        part: $part,
                        imagePath: $imagePath,
                        imageType: $imageType,
                        imageDescription: $chant['description'] ?? null,
                        verset: $chant['verset'] ?? null
                    );
                    $parts[$part->value] = $proper;
                }
            }
            return new CelebrationPropers(
                title: $title,
                rank: $rank,
                introitus: $parts[LiturgyPart::Introitus->value] ?? null,
                offertorium: $parts[LiturgyPart::Offertorium->value] ?? null,
                communio: $parts[LiturgyPart::Communio->value] ?? null
            );
        }
        return null;
    }
}
