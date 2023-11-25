<?php
use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
/** @var ControllerResult $result */

?>
<?php /** @var ChantItem $part */
foreach ($result->get('parts') as $part):?>

        <h3><?=$part->title?></h3>
    <?php if ($SVGPath = $part->SVGPath):?>
            <p style="text-align: center;background-color: antiquewhite">
                <br>
                    <img src="<?=$SVGPath?>" style="background-color: antiquewhite">
                <br>
                <br>
            </p>
    <?php endif;
    if ($HTMLPath = $part->HTMLPath):
        include_once $HTMLPath;
    endif;?>
<?php endforeach?>

