<?php

namespace App\Lib\Enum;

enum ImageType: string
{
    case WEBP = ".webp";
    case PNG = ".png";
    case SVG = ".svg";
    case PDF = ".pdf";
}