<?php

namespace App\Controllers;

use App\View\Title;

class MainController extends BaseController
{
    public static function main() {
        Title::set('Graduale Simplex');
        self::render('main');
    }
}
