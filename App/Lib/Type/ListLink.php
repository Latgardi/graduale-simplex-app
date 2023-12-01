<?php

namespace App\Lib\Type;

class ListLink
{
    public function __construct(
        public string $title,
        public ?string $link = null,
        public ?string $number = null
    ) {}
}