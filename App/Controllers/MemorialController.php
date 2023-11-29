<?php

namespace App\Controllers;

use App\Controllers\FeastController;

class MemorialController extends FeastController
{
    protected string $feastsStorageDir = '/library/memorials/';
    protected string $feastsWebPath = '/chants/memorials/';
    protected string $feastsTitle = 'memorials';
    protected string $feastTitle = 'memoria';
}