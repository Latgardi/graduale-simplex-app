<?php

namespace App\Lib\Type;

use App\Lib\Enum\ImageType;

class ChantItem
{
    public function __construct(
        public string     $title,
        public ?string    $imagePath = null,
        public ?ImageType $imageType = null,
        public ?string    $imageDescription = null,
        public ?string    $HTMLPath = null
    ) {}

    public function isEmpty(): bool
    {
        return is_null($this->imagePath) && is_null($this->HTMLPath);
    }
}
