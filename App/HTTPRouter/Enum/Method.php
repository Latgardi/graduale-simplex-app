<?php

namespace App\HTTPRouter\Enum;
require dirname(__DIR__, 3) . '/vendor/autoload.php';

enum Method: string
{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case DELETE = 'delete';
}
