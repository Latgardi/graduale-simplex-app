<?php
use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
/** @var ControllerResult $result */

?>
<?php /** @var ChantItem $part */
foreach ($result->get('parts') as $part):?>
    <details>
        <summary><?=$part->title?></summary>
    <?php if ($ImagePath = $part->imagePath):
        if ($part->imageType === \App\Lib\Enum\ImageType::PDF):?>
            <object data="<?=$ImagePath?>" type="application/pdf" width="100%" height="500px">
            </object>
        <?php else:?>
            <p style="text-align: center;background-color: antiquewhite">
                <br>
                    <img src="<?=$ImagePath?>" style="background-color: antiquewhite">
                <br>
                <br>
            </p>
    <?php endif;
    endif;
    if ($HTMLDesription = $part->imageDescription):?>
        <p class="notice">
            <?=$HTMLDesription?>
        </p>
    <?php endif;
    if ($HTMLPath = $part->HTMLPath):
        include_once $HTMLPath;
    endif;?>
    </details>
<?php endforeach?>

