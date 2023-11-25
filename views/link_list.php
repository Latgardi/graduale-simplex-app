<?php

use App\Lib\Type\ControllerResult;
/** @var ControllerResult $result */
?>

<article>
    <?php foreach ($result->get('links') as $link):?>
        <p>
            <code>></code>
            <a href="<?=$link['link']?>">
                <?=$link['title']?>
            </a>
        </p>
    <?php endforeach?>
</article>
