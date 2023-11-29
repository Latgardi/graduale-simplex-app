<?php

namespace App\Controllers;

use App\View\Title;

class AboutController extends BaseController
{
    public function index(): void
    {
        Title::set('Пра праект');
        $this->render('about');
    }
}