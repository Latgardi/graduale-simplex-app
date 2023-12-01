<?php

namespace App\Controllers;

use App\Config;
use App\HTTPRouter\Dispatcher;
use App\Lib\Enum\ImageType;
use App\Lib\Enum\LiturgyPart;
use App\Lib\Type\CelebrationPropers;
use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
use App\Lib\Type\ListLink;
use App\Localization\LocalizedCelebration;
use App\Localization\LocalizedName;
use App\View\Title;
use DirectoryIterator;
use GradualeSimplex\LiturgicalCalendar\Enum\CelebrationRank;
use GradualeSimplex\LiturgicalCalendar\LiturgicalCalendar;
use GradualeSimplex\LiturgicalCalendar\Utility\SolemnitySlug;
use PDOStatement;

abstract class BaseController
{
    protected const string SCORES_DIR = '/scores/';
    protected function render(string $view, ?ControllerResult $result = null): void
    {
        $viewsFolder = Config::get('views_folder');
        require_once $viewsFolder . '/layout/header.php';
        require_once $viewsFolder . '/' . $view . '.php';
        require_once $viewsFolder . '/layout/footer.php';
    }

    protected function getCelebrationPropers(PDOStatement $pdo): ?CelebrationPropers
    {
        $celebration = $pdo->fetch();
        if (is_bool($celebration)) {
            return null;
        }
        $title = $celebration['title'] ?? '';
        $rank = CelebrationRank::tryFromString($celebration['rank']);
        $parts = [];
        foreach (LiturgyPart::cases() as $part) {
            $partName = strtolower($part->name);
            $chantTitle = $celebration["{$partName}_title"] ?? null;
            $imageType = null;
            $imagePath = null;
            if (!is_null($chantTitle)) {
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
                    imageDescription: $celebration["{$partName}_description"] ?? null,
                    verset: $celebration["{$partName}_verset"] ?? null
                );
                $parts[$part->name] = $proper;
            }
        }
        return new CelebrationPropers(
            title: $title,
            rank: $rank,
            introitus: $parts[LiturgyPart::Introitus->name] ?? null,
            offertorium: $parts[LiturgyPart::Offertorium->name] ?? null,
            communio: $parts[LiturgyPart::Communio->name] ?? null
        );
    }

    protected function renderCelebrationList(?PDOStatement $celebrations, string $URLPrefix): void
    {
        $result = new ControllerResult();
        $links = [];
        if (!is_null($celebrations)) {
            foreach ($celebrations as $celebration) {
                $links[] = new ListLink(
                    title: LocalizedName::for($celebration['title']) ?? $celebration['title'],
                    link: $URLPrefix . $celebration['slug']
                );
            }
        }
        $result->set('links', $links);
        $this->render('link_list', $result);
    }

    protected function renderLiturgicalDay(?PDOStatement $dayPDO, bool $setTitle): void
    {
        $result = new ControllerResult();
        if (is_null($dayPDO)) {
            Dispatcher::return404();
        }
        $celebrationPropers = $this->getCelebrationPropers(pdo: $dayPDO);
        if (is_null($celebrationPropers)) {
            Dispatcher::return404();
        }
        $localizedCelebration = new LocalizedCelebration($celebrationPropers->title);
        if ($setTitle) {
            $title = $localizedCelebration->getTranslation();
            $localizedRankName = LocalizedName::for($celebrationPropers->rank->value);
            Title::set($title, $localizedRankName);
            $result->set('title', $title);
        }
        $result->set('propers', $celebrationPropers);
        $this->render('liturgical_day', $result);
    }

    protected function getDirectoryFilesLinks(
        string $relativeDirectoryPath,
        string $webPath,
        bool $isSolemnity = true
    ): array
    {
        $liturgicalCalendar = new LiturgicalCalendar();
        $links = [];
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . $relativeDirectoryPath)) {
            foreach (new DirectoryIterator(
                         directory: $_SERVER['DOCUMENT_ROOT'] . $relativeDirectoryPath
                     ) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                $title = $fileInfo->getFilename();
                if ($isSolemnity) {
                    $title = SolemnitySlug::solemnitySlugToTitle($title);
                } else {
                    $title = $liturgicalCalendar->getTitleForSlug(slug: $title);
                }
                if ($localizedTitle = LocalizedName::for($title)) {
                    $title = $localizedTitle;
                }
                $links[] = [
                    'link' => $webPath . $fileInfo->getFilename(),
                    'title' => $title
                ];
            }
        }
        return $links;
    }
}
