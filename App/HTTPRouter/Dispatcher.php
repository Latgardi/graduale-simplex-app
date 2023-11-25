<?php
namespace App\HTTPRouter;
use App\HTTPRouter\Enum\Method;
use App\View\Title;
use JetBrains\PhpStorm\NoReturn;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

class Dispatcher
{
    private Method $requestMethod;
    private string $requestUri;

    public function __construct(Method $requestMethod, $requestUri)
    {
        $this->requestMethod = $requestMethod;
        $this->requestUri = $requestUri;
    }

    #[NoReturn] public static function return404(): never
    {
        http_response_code(404);
        require_once dirname(__DIR__, 2) . '/views/404.php';
        die();
    }

    public function dispatch()
    {
        foreach (Route::getRoutes(for: $this->requestMethod) as $route => $action) {
            $this->processRoute(route: $route, action: $action);
        }

        self::return404();
    }

    private function processRoute(string $route, callable|string $action): void
    {
        //var_dump($route);
        if (!is_callable($action)) {
            if (!strpos($action, '.php')) {
                $action .= '.php';
            }
        }
        if ($route === "/404") {
            include_once __DIR__ . "/$action";
            exit();
        }
        $requestURL = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        $requestURL = strtok($requestURL, '?');
        $requestURL = rtrim($requestURL, '/');
        $requestURL = strtok($requestURL, '?');
        $routeParts = explode('/', $route);
        $requestURLParts = explode('/', $requestURL);
        array_shift($routeParts);
        array_shift($requestURLParts);
        if ($routeParts[0] === '' && count($requestURLParts) === 0) {
            // Callback function
            if (is_callable($action)) {
                call_user_func_array($action, []);
                exit();
            }
            include_once __DIR__ . "/$action";
            exit();
        }
        if (count($routeParts) !== count($requestURLParts)) {
            return;
        }
        $parameters = [];
        for ($__i__ = 0, $__i__Max = count($routeParts); $__i__ < $__i__Max; $__i__++) {
            $routePart = $routeParts[$__i__];
            if (preg_match("/^[$]/", $routePart)) {
                $routePart = ltrim($routePart, '$');
                $parameters[] = $requestURLParts[$__i__];
                $$routePart = $requestURLParts[$__i__];
            } else if ($routeParts[$__i__] !== $requestURLParts[$__i__]) {
                return;
            }
        }
        // Callback function
        if (is_callable($action)) {
            call_user_func_array($action, $parameters);
            exit();
        }
        include_once $_SERVER['DOCUMENT_ROOT'] . "/$action";
        exit();
    }
}
