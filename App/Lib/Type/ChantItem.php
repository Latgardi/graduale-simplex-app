<?php

namespace App\Lib\Type;

use App\Lib\Enum\ImageType;
use App\Lib\Enum\LiturgyPart;

class ChantItem
{
    public function __construct(
        public string     $title,
        public ?LiturgyPart $part,
        public ?string    $imagePath = null,
        public ?ImageType $imageType = null,
        public ?string    $imageDescription = null,
        public ?string    $verset = null
    ) {}

    public function isEmpty(): bool
    {
        return is_null($this->imagePath) && is_null($this->verset);
    }
}
