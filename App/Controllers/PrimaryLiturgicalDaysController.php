<?php

namespace App\Controllers;

use App\HTTPRouter\Dispatcher;
use App\Lib\Enum\ImageType;
use App\Lib\Enum\LiturgyParts;
use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
use App\Lib\Utility\NameConverter;
use App\Localization\LocalizedName;
use App\View\Title;
use DirectoryIterator;
use GradualeSimplex\LiturgicalCalendar\Utility\IntToRoman;

class PrimaryLiturgicalDaysController extends BaseController
{
    // PLD – Primary Liturgical Days
    private const PLD_STORAGE_DIR = '/library/primary-liturgical-days/';
    private const PLD_WEB_PATH = '/chants/primary-liturgical-days/';
    public static function days(): void
    {
        Title::set(LocalizedName::for('primary liturgical days'));
        $result = new ControllerResult();
        $links = self::getDirectoryFilesLinks(
            relativeDirectoryPath: self::PLD_STORAGE_DIR,
            webPath: self::PLD_WEB_PATH
        );

        $result->set('links', $links);
        self::render('link_list', $result);
    }

    public static function day(string $name, bool $setTitle = true): void
    {
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . self::PLD_STORAGE_DIR . $name)) {
            $result = new ControllerResult();
            if ($setTitle) {
                $title = NameConverter::webToTitle($name);
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
                $links = self::getDirectoryFilesLinks(
                    relativeDirectoryPath: self::PLD_STORAGE_DIR . $name . '/',
                    webPath: self::PLD_WEB_PATH . $name . '/'
                );
                $result->set('links', $links);
                self::render('link_list', $result);
            } else {
                $relativeDirName = self::PLD_STORAGE_DIR . $name;
                $absoluteDirName = $_SERVER['DOCUMENT_ROOT'] . $relativeDirName;
                $parts = self::getLiturgicalParts(absolutePath: $absoluteDirName, relativePath: $relativeDirName);
                $result->set('parts', $parts);
                self::render('liturgical_day', $result);
            }
        } else {
            Dispatcher::return404();
        }
    }

    public static function mass(string $day, string $name): void {
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . self::PLD_STORAGE_DIR . $day . '/' . $name)) {
            $result = new ControllerResult();
            $titleDay = LocalizedName::for(NameConverter::webToTitle($day));
            $titleMass = LocalizedName::for(NameConverter::webToTitle($name));
            Title::set($titleDay . ', ' . $titleMass);
            $relativeDirName = self::PLD_STORAGE_DIR . $day . '/' . $name;
            $absoluteDirName = $_SERVER['DOCUMENT_ROOT'] . $relativeDirName;
            $parts = self::getLiturgicalParts(absolutePath: $absoluteDirName, relativePath: $relativeDirName);
            $result->set('parts', $parts);
            self::render('liturgical_day', $result);
        }
    }
}
