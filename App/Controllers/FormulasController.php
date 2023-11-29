<?php

namespace App\Controllers;

use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
use App\Localization\LocalizedName;
use App\View\Title;
use GradualeSimplex\LiturgicalCalendar\Utility\RomanNumber;

class FormulasController extends BaseController
{
    public function formulas(): void
    {
        Title::set(LocalizedName::for('formulas_short'));
        $result = new ControllerResult();
        $formulasSources = [];
        foreach (range(1, 9) as $number) {
            if (file_exists($this->getFormulasDir(abs: true) . $number . '.webp')) {
                $number = new RomanNumber(number: $number);
                $formulasSources[] = new ChantItem(
                    title: $number === 9 ? 'Tonus peregrinus' : 'Tonus ' . $number->getRomanValue(),
                    imagePath: $this->getFormulasDir() . $number . '.webp'
                );
            }
        }
        $result->set('formulas', $formulasSources);
        $this->render('formulas', $result);
    }

    private function getFormulasDir(bool $abs = false): string
    {
        $dirName = '/library/formulas/';
        return $abs ? $_SERVER['DOCUMENT_ROOT'] . $dirName : $dirName;
    }
}