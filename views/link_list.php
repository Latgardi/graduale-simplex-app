<?php

use App\Lib\Type\ControllerResult;
/** @var ControllerResult $result */
?>

<article>
    <?php foreach ($result->get('links') as $link):?>
        <p>
            <code>></code>
            <?php // TODO dev solution; remove
            if (is_null($link['link'])):?>
                <span>
                    <?=$link['title']?>
                </span>
            <?php else: ?>
                <a href="<?=$link['link']?>">
                    <?=$link['title']?>
                </a>
            <?php endif;?>
        </p>
    <?php endforeach?>
</article>
