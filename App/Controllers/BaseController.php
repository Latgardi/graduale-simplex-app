<?php

namespace App\Controllers;

use App\Config;
use App\Lib\Enum\ImageType;
use App\Lib\Enum\LiturgyParts;
use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
use App\Lib\Utility\NameConverter;
use App\Localization\LocalizedName;
use DirectoryIterator;

class BaseController
{
    protected static function render(string $view, ?ControllerResult $result = null): void
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
    protected static function getLiturgicalParts(string $absolutePath, string $relativePath): array
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

    protected static function getDirectoryFilesLinks(string $relativeDirectoryPath, string $webPath): array
    {
        $links = [];
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . $relativeDirectoryPath)) {
            foreach (new DirectoryIterator(
                         directory: $_SERVER['DOCUMENT_ROOT'] . $relativeDirectoryPath
                     ) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                $title = NameConverter::webToTitle($fileInfo->getFilename());
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
