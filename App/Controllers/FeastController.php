<?php

namespace App\Controllers;

use App\HTTPRouter\Dispatcher;
use App\Lib\Type\ControllerResult;
use App\Localization\LocalizedName;
use App\View\Title;
use GradualeSimplex\LiturgicalCalendar\LiturgicalCalendar;
use GradualeSimplex\LiturgicalCalendar\Utility\SolemnitySlug;

class FeastController extends BaseController
{
    protected string $feastsStorageDir = '/library/feasts/';
    protected string $feastsWebPath = '/chants/feasts/';
    protected string $feastsTitle = 'feasts';
    protected string $feastTitle = 'festum';
    public function feasts(): void
    {
        Title::set(LocalizedName::for($this->feastsTitle));
        $result = new ControllerResult();
        $links = $this->getDirectoryFilesLinks(
            relativeDirectoryPath: $this->feastsStorageDir,
            webPath: $this->feastsWebPath,
            isSolemnity: false
        );
        $result->set('links', $links);
        $this->render('link_list', $result);
    }

    public function feast(string $name, bool $setTitle = true): void
    {
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . $this->feastsStorageDir . $name)) {
            $result = new ControllerResult();
            $relativeDirName = $this->feastsStorageDir . $name;
            $absoluteDirName = $_SERVER['DOCUMENT_ROOT'] . $relativeDirName;
            $parts = $this->getLiturgicalParts(absolutePath: $absoluteDirName, relativePath: $relativeDirName);
            if ($setTitle) {
                $liturgicalCalendar = new LiturgicalCalendar();
                $title = $liturgicalCalendar->getTitleForSlug(slug: $name);
                if ($localizedTitle = LocalizedName::for($title)) {
                    $title = $localizedTitle;
                }
                $localizedRankName = LocalizedName::for($this->feastTitle);
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
