<?php
error_reporting(E_ALL);

use App\Lib\Enum\ImageType;
use App\Lib\Type\CelebrationPropers;
use App\Lib\Type\ChantItem;
use App\Lib\Type\ControllerResult;
/** @var ControllerResult $result */
/** @var CelebrationPropers $propers */
$propers = $result->get('propers');
foreach ($propers->getChants() as $chant):?>
    <details>
        <summary><?=$chant->part->name?></summary>
    <?php if ($ImagePath = $chant->imagePath):
        if ($chant->imageType === ImageType::PDF):?>
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
    if ($HTMLDescription = $chant->imageDescription):?>
        <p class="notice">
            <?=$HTMLDescription?>
        </p>
    <?php endif;
    if ($verset = $chant->verset):
        echo $verset;
    endif;?>
    </details>
<?php endforeach;
