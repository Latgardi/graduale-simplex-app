<?php

namespace App\View;

use App\View\Type\MenuItem;

class Menu
{
    /**
     * @param array<MenuItem> $entries
     */
    public function __construct(
        public array $entries
    ) {}

    public function render(): void
    {
        ?>
        <nav>
            <ul>
                <?php foreach ($this->entries as $entry):?>
                    <li>
                        <a href="<?=$entry->link?>" <?=$this->isCurrent($entry->link) ? ' class="current"' : ''?>>
                            <?=$entry->title?>
                        </a>
                    </li>
                <?php endforeach;?>
            </ul>
        </nav>
        <?php
    }

    private function isCurrent(string $link): bool
    {
        return $link === '/'
            ? $_SERVER['REQUEST_URI'] === '/'
            : str_contains($_SERVER["REQUEST_URI"], $link);
    }
}