<?php

use App\View\Title;
use GradualeSimplex\LiturgicalCalendar\Enum\Season;
use GradualeSimplex\LiturgicalCalendar\Utility\IntToRoman;
/** @var ControllerResult $result */
?>

<article>
    <?php foreach ($result->get('links') as $link):?>
        <p>
            <code>></code>
            <a href="<?=$link['link']?>"><?=$link['title']?></a>
        </p>
    <?php endforeach?>
</article>
