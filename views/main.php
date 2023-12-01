<?php
//$season = $today->season;
//$week = $today->seasonWeek;

/** @var LiturgicalDay $today */

use App\Controllers\SeasonController;
use GradualeSimplex\LiturgicalCalendar\Type\LiturgicalDay;

$season = $today->season;
$week = $today->seasonWeek; ?>
    <p class="notice today-liturgy"><b>Сёньня ў літургіі:</b></p>
<?php
(new SeasonController())->day(season: $season->name, week: $week, setTitle: false);
