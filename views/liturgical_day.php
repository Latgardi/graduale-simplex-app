<?php
error_reporting(E_ALL);

use App\Lib\Enum\ImageType;
use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
/** @var ControllerResult $result */
/** @var ChantItem $part */
ob_start();
foreach ($result->get('parts') as $part):?>
    <details>
        <summary><?=$part->title?></summary>
    <?php if ($ImagePath = $part->imagePath):
        if ($part->imageType === ImageType::PDF):?>
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
<?php endforeach;
