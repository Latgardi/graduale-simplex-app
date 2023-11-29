<?php

namespace App\HTTPRouter;
use App\HTTPRouter\Enum\Method;

require dirname(__DIR__, 2) . '/vendor/autoload.php';
class Route
{
    private static array $routes = [];

    public static function getRoutes(Method $for): array
    {
        return self::$routes[$for->value] ?? [];
    }

    public static function addRoute(Method $method, string $route, callable|string $action): void
    {
        self::$routes[$method->value][$route] = $action;
    }

    public static function get(string $route, callable|string $action): void
    {
        self::addRoute(method: Method::GET, route: $route, action: $action);
    }
}
