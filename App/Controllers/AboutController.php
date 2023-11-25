<?php

namespace App\Controllers;

use App\View\Title;

class AboutController extends BaseController
{
    public static function index(): void
    {
        Title::set('Пра праект');
        self::render('about');
    }
}