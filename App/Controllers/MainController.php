<?php

namespace App\Controllers;

use App\View\Title;

class MainController extends BaseController
{
    public function main() {
        Title::set('Graduale Simplex');
        $this->render('main');
    }
}
