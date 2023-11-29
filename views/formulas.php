<?php
use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
/** @var ControllerResult $result */

?>
<?php /** @var ChantItem $part */
foreach ($result->get('formulas') as $formula):?>
    <details>
        <summary><?=$formula->title?></summary>
            <p style="text-align: center;background-color: antiquewhite">
                <br>
                    <img src="<?=$formula->imagePath?>" style="background-color: antiquewhite">
                <br>
                <br>
            </p>
    </details>
<?php endforeach?>

