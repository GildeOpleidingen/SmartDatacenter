<?php
namespace App\Routing;

use App\Controllers\Controller;

final class Router {
    private array $routes = [];

    public function get(string $path, string $class, string $action) : void {
        $this->addRoute('GET', $path, $class, $action);
    }

    public function post(string $path, string $class, string $action) : void {
        $this->addRoute('POST', $path, $class, $action);
    }

    private function addRoute(string $method, string $path, string $class, string $action) : void {
    $this->routes[] = [
        'method' => $method,
        'path' => $path,
        'class' => $class,
        'action' => $action,
    ];
    }

    public function dispatch() : void {
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                $controllerClass = new $route['class'];
                $action = $route['action'];

                $controller = new $controllerClass();

                if (!method_exists($controller, $action)) {
                    http_response_code(500);
                    echo '500 - Controller action not found';
                    return;
                }

                $controller->$action();
                return;
            }
        }

        http_response_code(404);
        echo '404 - Page not found';
    }
}