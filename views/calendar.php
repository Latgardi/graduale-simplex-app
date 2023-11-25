<?php

use App\Lib\Type\ControllerResult;
use App\Localization\LocalizedName;
use GradualeSimplex\LiturgicalCalendar\Enum\Language;
use GradualeSimplex\LiturgicalCalendar\Enum\Month;use GradualeSimplex\LiturgicalCalendar\Type\LiturgicalDay;


/** @var ControllerResult $result */
?>
<p class="notice">Каляндар на бягучы месяц</p>
<h2><?= $result->get('title') ?></h2>
<div>
    <code>Месяц</code>
    &nbsp;
    <select onchange="window.location.href = value">
        <?php foreach ($result->get('months') as $month): ?>
            <option<?= $month['current'] ? ' selected' : '' ?> value="<?= $month['link']?>">
                <?= $month['title'] ?>
            </option>
        <?php endforeach; ?>
    </select>
    &nbsp;
    &nbsp;
    &nbsp;
    <code>Год</code>
    &nbsp;
    <select onchange="window.location.href = value + <?=$result->get('currentMonth')?> + '/'">
        <?php foreach ($result->get('years') as $year): ?>
            <option<?= $year['current'] ? ' selected' : '' ?> value="<?= $year['link']?>">
                <?= $year['title'] ?>
            </option>
        <?php endforeach; ?>
    </select>
    &nbsp;
    &nbsp;
    &nbsp;
    <code>Мова</code>
    &nbsp;
    <select onchange="window.location.search = 'lang=' + value">
        <?php foreach (Language::cases() as $language): ?>
            <option<?= $_GET['lang'] === $language->value ? ' selected' : '' ?> value="<?= $language->value?>">
                <?= LocalizedName::for($language->name) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<table>
    <thead>
    <tr>
        <th>Дзень</th>
        <th>Цэлебрацыі</th>
        <th>Пэрыяд</th>
    </tr>
    </thead>
    <tbody>
    <?php /** @var LiturgicalDay $day */
    foreach ($result->get('calendar') as $day):
        ?>
        <tr>
            <td<?=$day->date->isToday() ? ' class="today"' : ''?>>
                <p>
                    <?= $day->date->format('j/n') ?>&nbsp;
                    <?= LocalizedName::for('short_' . $day->weekday->name) ?>
                </p>
            </td>
            <td>
                <?php foreach ($day->celebrations as $celebration): ?>
                    <?php if ($comment = $celebration->comment):?>
                        <p>
                            <i><?=$comment?></i>
                        </p>
                    <?php endif;?>
                    <p>
                        <?php $colourValue = $celebration->colour->value;
                        if ($colourValue === 'violet') {
                            $colourValue = "purple";
                        }
                        ?>
                        <span class="dot" style="background-color: <?= $colourValue ?>"></span>
                        <?php if (!is_null($celebration->chantLink)):?>
                        <a href="<?=$celebration->chantLink?>"><b><?= $celebration->title ?></b></a>
                        <?php else:?>
                        <b><?= $celebration->title ?></b>
                        <?php endif?>
                        &nbsp;
                        <code><?=
                            $result->get('language') === Language::Belarusian
                                ? (LocalizedName::for($celebration->rank->value) ?? $celebration->rank->value)
                                : $celebration->rank->value
                            ?></code>
                    </p>
                <?php endforeach; ?>
            </td>
				<td>
				<p><?= $result->get('language') === Language::Belarusian
                                ? (LocalizedName::for($day->season->name) ?? $day->season->name)
                                : $day->season->name ?></p>
				</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="float-parent-element">
    <div class="float-child-element">
        <a class="button " href="<?=$result->get('prevMonth')['link']?>">
            << <?=$result->get('prevMonth')['title']?>
        </a>
    </div>
    <div class="float-child-element">
        <a class="button" href="<?=$result->get('nextMonth')['link']?>">
            <?=$result->get('nextMonth')['title']?> >>
        </a>
    </div>
</div>

<div style="display: flex;">

</div>

