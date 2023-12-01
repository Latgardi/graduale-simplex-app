<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Lib\Enum\CategoryWorkingStatus;
use App\Lib\Enum\ChantCategory;
use App\Lib\Type\ControllerResult;
use App\Lib\Type\ListLink;
use App\Localization\LocalizedName;
use App\View\Title;
use GradualeSimplex\LiturgicalCalendar\Enum\Season;

class ChantController extends BaseController
{
    public function categoryList(): void
    {
        $result = new ControllerResult();
        Title::set(LocalizedName::for('chants'));
        $categories = [];
        foreach (ChantCategory::cases() as $category) {
            $categoryName = strtolower(str_replace('_', '-', $category->name));
            if ($category->getStatus() === CategoryWorkingStatus::IsEmptyYet) {
                $link = null;
            } else {
                $link = '/chants/' . $categoryName . '/';
            }
            $categories[] = new ListLink(
                title: LocalizedName::for(str_replace('-', ' ', $categoryName)),
                link: $link
            );
        }
        $result->set('links', $categories);
        $this->render('link_list', $result);
    }
}
