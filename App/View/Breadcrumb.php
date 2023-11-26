<?php

namespace App\View;

use App\Lib\Utility\NameConverter;
use App\Localization\LocalizedName;
use App\View\Type\BreadcrumbItem;
use Bitrix\Bizproc\Workflow\Template\Packer\Result\Pack;

class Breadcrumb
{
    public array $items = [];
    public function __construct() {
        $urlParts = $this->getURLParts();
        $this->items = $this->getBreadcrumbItems($urlParts);
    }

    public function render(): void
    {
        if (!$this->isMainPage()) {
            echo "<ul class='breadcrumb'>";
            foreach ($this->items as $item) {
                echo "<li>";
                if ($item->isCurrent) {
                    echo "<span>$item->title";
                } else {
                    echo "<a href=\"$item->link\">$item->title</a>";
                }
                echo "</li>";
            }
            echo "</ul>";
        }
    }

    private function getBreadcrumbItems(array $URLParts): array
    {
        $items = [];
        $link = '/';
        $partsCount = count($URLParts);
        foreach ($URLParts as $index => $part) {
            if ($index > 0) {
                $link .= $part;
            }
            $isCurrent =
                ($link === $_SERVER['REQUEST_URI'])
                || ($link . '/' === $_SERVER['REQUEST_URI'])
            ;
            if ($index > 0) {
                $link .= '/';
            }
            if (($index === $partsCount - 1) && Title::getTitle() !== '') {
                $title = Title::getTitle();
            } else {
                $name = NameConverter::webToTitle($part);
                $title = LocalizedName::for($name) ?? $name;
            }
            $items[] = new BreadcrumbItem(
                title: $title,
                link: $link,
                isCurrent: $isCurrent
            );
        }
        return $items;
    }

    private function getURLParts(): array
    {
        $urlParts = explode('/' , $_SERVER['REQUEST_URI']);
        $urlParts[0] = 'index';
        $urlPartsCount = count($urlParts);
        if (
            $urlParts[$urlPartsCount - 1] === '/'
            || $urlParts[$urlPartsCount - 1] === ''
        ) {
            unset($urlParts[$urlPartsCount - 1]);
        }
        return $urlParts;
    }

    private function isMainPage(): bool
    {
        return $_SERVER['REQUEST_URI'] === '/';
    }
    //private function getBreadcrumbLinkItems
}
