<?php

namespace GradualeSimplex\LiturgicalCalendar\Utility;

class SolemnitySlug
{


    public static function solemnitySlugToTitle(string $slug): string
    {
        return str_replace('-', ' ', $slug);
    }

    public static function generateSlug($title): string
    {
        $title = iconv('utf-8', 'CP1251//TRANSLIT', $title);

        $title = str_replace([',', '.', ' '], '-', $title);

        $title = preg_replace('/[^A-Za-z0-9-]+/', '', $title);
        return strtolower($title);
    }
}
