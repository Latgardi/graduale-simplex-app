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
    private const SOLEMNITIES_STORAGE_DIR = '/library/solemnities/';
    private const SOLEMNITIES_WEB_PATH = '/chants/solemnities/';
    public static function solemnities(): void
    {
        Title::set(LocalizedName::for('solemnities'));
        $result = new ControllerResult();
        $links = self::getDirectoryFilesLinks(
            relativeDirectoryPath: self::SOLEMNITIES_STORAGE_DIR,
            webPath: self::SOLEMNITIES_WEB_PATH
        );
        $result->set('links', $links);
        self::render('link_list', $result);
    }

    public static function solemnity(string $name, bool $setTitle = true): void
    {
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . self::SOLEMNITIES_STORAGE_DIR . $name)) {
            $result = new ControllerResult();
            $relativeDirName = self::SOLEMNITIES_STORAGE_DIR . $name;
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
