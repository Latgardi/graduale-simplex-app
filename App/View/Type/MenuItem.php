<?php

namespace App\View\Type;

class MenuItem
{
    public function __construct(
        public string $title,
        public string $link
    ) {}
}