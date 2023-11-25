<?php

use App\Lib\Type\ControllerResult;
use GradualeSimplex\LiturgicalCalendar\Enum\Season;

/** @var ControllerResult $result */
//var_dump($result);die();
?>
<article>
<?php foreach ($result->get('seasons') as $season): ?>
    <p>
        <code>></code>
        <a href="<?= $season['link'] ?>">
            <?= $season['name'] ?>
        </a>
    </p>
<?php endforeach ?>
</article>
