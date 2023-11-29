<?php

namespace App\Controllers;

use App\HTTPRouter\Dispatcher;
use App\Lib\Type\ControllerResult;
use App\Localization\LocalizedName;
use App\View\Title;
use GradualeSimplex\LiturgicalCalendar\Utility\SolemnitySlug;

class PrimaryLiturgicalDaysController extends BaseController
{
    // PLD – Primary Liturgical Days
    private const PLD_STORAGE_DIR = '/library/primary-liturgical-days/';
    private const PLD_WEB_PATH = '/chants/primary-liturgical-days/';
    public function days(): void
    {
        Title::set(LocalizedName::for('primary liturgical days'));
        $result = new ControllerResult();
        $links = $this->getDirectoryFilesLinks(
            relativeDirectoryPath: self::PLD_STORAGE_DIR,
            webPath: self::PLD_WEB_PATH
        );

        $result->set('links', $links);
        $this->render('link_list', $result);
    }

    public function day(string $name, bool $setTitle = true): void
    {
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . self::PLD_STORAGE_DIR . $name)) {
            $result = new ControllerResult();
            if ($setTitle) {
                $title = SolemnitySlug::solemnitySlugToTitle($name);
                if ($localizedTitle = LocalizedName::for($title)) {
                    $title = $localizedTitle;
                }
                $localizedRankName = LocalizedName::for('primary liturgical day');
                Title::set($title, $localizedRankName);
                $result->set('title', $title);
            }
            $hasMultipleMasses = count(glob(
                $_SERVER['DOCUMENT_ROOT'] . self::PLD_STORAGE_DIR . $name . '/*', GLOB_ONLYDIR
            )) > 0;
            if ($hasMultipleMasses) {
                $links = $this->getDirectoryFilesLinks(
                    relativeDirectoryPath: self::PLD_STORAGE_DIR . $name . '/',
                    webPath: self::PLD_WEB_PATH . $name . '/'
                );
                $result->set('links', $links);
                $this->render('link_list', $result);
            } else {
                $relativeDirName = self::PLD_STORAGE_DIR . $name;
                $absoluteDirName = $_SERVER['DOCUMENT_ROOT'] . $relativeDirName;
                $parts = $this->getLiturgicalParts(absolutePath: $absoluteDirName, relativePath: $relativeDirName);
                $result->set('parts', $parts);
                $this->render('liturgical_day', $result);
            }
        } else {
            Dispatcher::return404();
        }
    }

    public function mass(string $day, string $name): void {
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . self::PLD_STORAGE_DIR . $day . '/' . $name)) {
            $result = new ControllerResult();
            $titleDay = LocalizedName::for(SolemnitySlug::solemnitySlugToTitle($day));
            $titleMass = LocalizedName::for(SolemnitySlug::solemnitySlugToTitle($name));
            Title::set($titleDay . ', ' . $titleMass);
            $relativeDirName = self::PLD_STORAGE_DIR . $day . '/' . $name;
            $absoluteDirName = $_SERVER['DOCUMENT_ROOT'] . $relativeDirName;
            $parts = $this->getLiturgicalParts(absolutePath: $absoluteDirName, relativePath: $relativeDirName);
            $result->set('parts', $parts);
            $this->render('liturgical_day', $result);
        }
    }
}
