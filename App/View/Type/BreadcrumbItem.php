<?php

namespace App\View\Type;

class BreadcrumbItem
{
    public function __construct(
        public string $title,
        public string $link,
        public bool $isCurrent
    ) {}
}
