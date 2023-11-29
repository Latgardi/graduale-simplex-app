<?php

namespace App\Controllers;

use App\HTTPRouter\Dispatcher;
use App\Lib\Type\ControllerResult;
use App\Localization\LocalizedName;
use App\View\Title;
use GradualeSimplex\LiturgicalCalendar\Utility\SolemnitySlug;

class SolemnityController extends BaseController
{
    private const SOLEMNITIES_STORAGE_DIR = '/library/solemnities/';
    private const SOLEMNITIES_WEB_PATH = '/chants/solemnities/';
    public function solemnities(): void
    {
        Title::set(LocalizedName::for('solemnities'));
        $result = new ControllerResult();
        $links = $this->getDirectoryFilesLinks(
            relativeDirectoryPath: self::SOLEMNITIES_STORAGE_DIR,
            webPath: self::SOLEMNITIES_WEB_PATH
        );
        $result->set('links', $links);
        $this->render('link_list', $result);
    }

    public function solemnity(string $name, bool $setTitle = true): void
    {
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . self::SOLEMNITIES_STORAGE_DIR . $name)) {
            $result = new ControllerResult();
            $relativeDirName = self::SOLEMNITIES_STORAGE_DIR . $name;
            $absoluteDirName = $_SERVER['DOCUMENT_ROOT'] . $relativeDirName;
            $parts = $this->getLiturgicalParts(absolutePath: $absoluteDirName, relativePath: $relativeDirName);
            if ($setTitle) {
                $title = SolemnitySlug::solemnitySlugToTitle($name);
                if ($localizedTitle = LocalizedName::for($title)) {
                    $title = $localizedTitle;
                }
                $localizedRankName = LocalizedName::for('sollemnitas');
                Title::set($title, $localizedRankName);
                $result->set('title', $title);
            }
            $result->set('parts', $parts);
            $this->render('liturgical_day', $result);
        } else {
            Dispatcher::return404();
        }
    }
}
