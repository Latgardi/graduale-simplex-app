<?php

use App\Lib\Type\ControllerResult;
use App\Lib\Type\ListLink;

/** @var ControllerResult $result */
?>
<?php if ($result->hasEmpty('links')):?>
    <h3>
        На жаль, тут <code class="sans">пакуль</code> нічога няма.
    </h3>
<?php else:?>
    <article>
        <?php /** @var ListLink $link */
        foreach ($result->get('links') as $link):?>
            <p>
                <code>></code>
                <?php // TODO dev solution; remove
                if (is_null($link->link)):?>
                    <span>
                        <?=$link->title?>
                    </span>
                <?php else: ?>
                    <a href="<?=$link->link?>">
                        <?=$link->title?>
                    </a>
                <?php endif;?>
            </p>
        <?php endforeach?>
    </article>
<?php endif;?>
