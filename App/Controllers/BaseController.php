<?php

namespace App\Controllers;

use App\Config;
use App\Lib\Enum\ImageType;
use App\Lib\Enum\LiturgyParts;
use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
use App\Localization\LocalizedName;
use DirectoryIterator;
use GradualeSimplex\LiturgicalCalendar\LiturgicalCalendar;
use GradualeSimplex\LiturgicalCalendar\Utility\SolemnitySlug;

abstract class BaseController
{
    protected function render(string $view, ?ControllerResult $result = null): void
    {
        $viewsFolder = Config::get('views_folder');
        require_once $viewsFolder . '/layout/header.php';
        require_once $viewsFolder . '/' . $view . '.php';
        require_once $viewsFolder . '/layout/footer.php';
    }

    /**
     * @param string $absolutePath
     * @param string $relativePath
     * @return array<ChantItem>
     */
    protected function getLiturgicalParts(string $absolutePath, string $relativePath): array
    {
        $parts = [];
        foreach (LiturgyParts::cases() as $case) {
            $part = new ChantItem(
                LocalizedName::for($case->name) ?? $case->name
            );
            $imageBaseFileName = '/' . strtolower($case->name);
            foreach (ImageType::cases() as $type) {
                if (file_exists($absolutePath . $imageBaseFileName . $type->value)) {
                    $part->imagePath = $relativePath . $imageBaseFileName . $type->value;
                    $part->imageType = $type;
                    break;
                }
            }
            $HTMLFileName = '/' . strtolower($case->name) . '.html';
            if (file_exists($absolutePath . $HTMLFileName)) {
                $part->HTMLPath = $absolutePath . $HTMLFileName;
            }
            $imageDescriptionFileName = '/' . strtolower($case->name) . '_description' . '.html';
            if (file_exists($absolutePath . $imageDescriptionFileName)) {
                $part->imageDescription = file_get_contents($absolutePath . $imageDescriptionFileName);
            }
            if (!$part->isEmpty()) {
                $parts[] = $part;
            }
        }
        return $parts;
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
