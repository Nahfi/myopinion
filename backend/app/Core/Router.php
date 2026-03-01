<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $pattern, array $handler, array $middlewares = []): void
    {
        $this->add('GET', $pattern, $handler, $middlewares);
    }

    public function post(string $pattern, array $handler, array $middlewares = []): void
    {
        $this->add('POST', $pattern, $handler, $middlewares);
    }

    public function put(string $pattern, array $handler, array $middlewares = []): void
    {
        $this->add('PUT', $pattern, $handler, $middlewares);
    }

    public function delete(string $pattern, array $handler, array $middlewares = []): void
    {
        $this->add('DELETE', $pattern, $handler, $middlewares);
    }

    public function patch(string $pattern, array $handler, array $middlewares = []): void
    {
        $this->add('PATCH', $pattern, $handler, $middlewares);
    }

    private function add(string $method, string $pattern, array $handler, array $middlewares): void
    {
        $regex = preg_replace('#:([a-zA-Z_]+)#', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        $this->routes[] = compact('method', 'pattern', 'regex', 'handler', 'middlewares');
    }

    public function dispatch(string $method, string $uri): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            if (preg_match($route['regex'], $uri, $matches)) {
                array_shift($matches);

                foreach ($route['middlewares'] as $middleware) {
                    (new $middleware())->handle();
                }

                [$class, $action] = $route['handler'];
                (new $class())->$action(...$matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['message' => 'Route not found']);
    }
}
