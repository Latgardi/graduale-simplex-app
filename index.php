<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config;
use App\HTTPRouter\Dispatcher;
use App\HTTPRouter\Request;
use App\Localization\LocalizedName;

Config::set('views_folder', $_SERVER['DOCUMENT_ROOT'] . '/views');
LocalizedName::init([
    __DIR__ . '/localization/localized-strings.json',
    __DIR__ . '/localization/localized-strings-feasts.json'
]);
require_once './routes.php';
$dispatcher = new Dispatcher(requestMethod: Request::getMethod(), requestUri: Request::getURI());
$dispatcher->dispatch();
