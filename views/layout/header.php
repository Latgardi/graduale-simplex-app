<?php
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Localization\LocalizedName;
use App\View\Breadcrumb;
use App\View\Menu;
use App\View\Title;
use App\View\Type\MenuItem;
use GradualeSimplex\LiturgicalCalendar\Enum\Language;
use GradualeSimplex\LiturgicalCalendar\LiturgicalCalendar;

$calendar = new LiturgicalCalendar();
$today = $calendar->getToday(language: Language::Belarusian);
$breadcrumb = new Breadcrumb();
$menu = new Menu(entries: [
    new MenuItem(
        title: 'Галоўная',
        link: '/'
    ),
    new MenuItem(
        title: 'Сьпевы',
        link: '/chants/'
    ),
    new MenuItem(
        title: 'Каляндар',
        link: '/calendar/'
    ),
    new MenuItem(
        title: 'Пра праект',
        link: '/about/'
    ),
])
?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Title::getTitle()?></title>
    <link rel="icon" sizes="64x64" href="/assets/images/favicon.png">
    <link rel="apple-touch-icon" href="/assets/images/favicon-source.png">
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<header>
    <h1><?= Title::getTitle()?></h1>
    <?php if ($subtitle = Title::getSubtitle()):?>
        <p><code class="sans"><?=$subtitle?></code></p>
    <?php endif;?>
    <?php $menu->render()?>
    <p>Сёньня Царква цэлебруе</p>
    <?php foreach ($today->celebrations as $celebration): ?>
        <?php if ($comment = $celebration->comment):?>
            <p>
                <i><?=$comment?></i>
            </p>
        <?php endif;?>
        <p>
            <span class="dot" style="background-color: <?= $celebration->colour->value ?>"></span>
            <b><?= $celebration->title ?></b>
        </p>
    <?php endforeach; ?>
</header>
<?php $breadcrumb->render();?>
