<?php

namespace App\Lib\Enum;

enum ImageType: string
{
    case PNG = ".png";
    case SVG = ".svg";
    case PDF = ".pdf";
    case WEBP = ".webp";
}