<?php

namespace App\Controllers;

use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
use App\Localization\LocalizedName;
use App\View\Title;
use GradualeSimplex\LiturgicalCalendar\Utility\IntToRoman;

class FormulasController extends BaseController
{
    public static function formulas(): void
    {
        Title::set(LocalizedName::for('formulas_short'));
        $result = new ControllerResult();
        $formulasSources = [];
        foreach (range(1, 9) as $number) {
            if (file_exists(self::getFormulasDir(abs: true) . $number . '.webp')) {
                $formulasSources[] = new ChantItem(
                    title: $number === 9 ? 'Tonus peregrinus' : 'Tonus ' . IntToRoman::convert($number),
                    imagePath: self::getFormulasDir() . $number . '.webp'
                );
            }
        }
        $result->set('formulas', $formulasSources);
        self::render('formulas', $result);
    }

    private static function getFormulasDir(bool $abs = false): string
    {
        $dirName = '/library/formulas/';
        return $abs ? $_SERVER['DOCUMENT_ROOT'] . $dirName : $dirName;
    }
}