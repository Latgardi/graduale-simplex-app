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

class SolemnityController extends BaseController
{
    private const SOLEMNITIES_DIR = '/library/solemnities/';
    public static function solemnities(): void
    {
        Title::set(LocalizedName::for('solemnities'));
        $result = new ControllerResult();
        $links = [];
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . self::SOLEMNITIES_DIR)) {
            foreach (new DirectoryIterator(
                         directory: $_SERVER['DOCUMENT_ROOT'] . self::SOLEMNITIES_DIR
                     ) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                $title = NameConverter::webToTitle($fileInfo->getFilename());
                if ($localizedTitle = LocalizedName::for($title)) {
                    $title = $localizedTitle;
                }
                $links[] = [
                    'link' => $fileInfo->getFilename(),
                    'title' => $title
                ];
            }
        }
        $result->set('links', $links);
        self::render('link_list', $result);
    }

    public static function solemnity(string $name, bool $setTitle = true): void
    {
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . self::SOLEMNITIES_DIR . $name)) {
            $result = new ControllerResult();
            $relativeDirName = '/library/solemnities/' . $name;
            $absoluteDirName = $_SERVER['DOCUMENT_ROOT'] . $relativeDirName;
            $parts = self::getLiturgicalParts(absolutePath: $absoluteDirName, relativePath: $relativeDirName);
            if ($setTitle) {
                $title = NameConverter::webToTitle($name);
                if ($localizedTitle = LocalizedName::for($title)) {
                    $title = $localizedTitle;
                }
                $localizedRankName = LocalizedName::for('sollemnitas');
                Title::set($title, $localizedRankName);
                $result->set('title', $title);
            }
            $result->set('parts', $parts);
            self::render('liturgical_day', $result);
        } else {
            Dispatcher::return404();
        }
    }
}