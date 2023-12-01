<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config;
use App\HTTPRouter\Dispatcher;
use App\HTTPRouter\Request;
use App\Localization\LocalizedName;

Config::set('views_folder', $_SERVER['DOCUMENT_ROOT'] . '/views');
Config::set('title', 'Graduale Simplex');
LocalizedName::init([
    __DIR__ . '/localization/localized-strings.json',
    __DIR__ . '/localization/localized-strings-feasts.json'
]);
\App\Database\Connector::init(__DIR__ . '/database/database.sqlite');
$pdo = \App\Database\Connector::getInstance()->getPDO();
/*$query = $pdo->query('SELECT * FROM celebrations');
foreach ($query as $row) {
    var_dump($row);
}
die();*/
//$pdo->commit();
require_once './routes.php';
$dispatcher = new Dispatcher(requestMethod: Request::getMethod(), requestUri: Request::getURI());
$dispatcher->dispatch();
