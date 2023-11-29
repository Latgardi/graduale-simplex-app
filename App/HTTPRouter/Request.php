<?php

namespace App\HTTPRouter;
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\HTTPRouter\Enum\Method;

class Request
{
    public static function getURI(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public static function getMethod(): ?Method
    {
        return Method::tryFrom(strtolower($_SERVER['REQUEST_METHOD']));
    }

}
