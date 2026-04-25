<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array  $routes     = [];
    private string $prefix     = '';
    private array  $middleware = [];

    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, string $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, string $handler): void
    {
        $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, string $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function group(string $prefix, callable $callback, array $middleware = []): void
    {
        $previousPrefix     = $this->prefix;
        $previousMiddleware = $this->middleware;

        $this->prefix     = $previousPrefix . $prefix;
        $this->middleware = array_merge($previousMiddleware, $middleware);

        $callback($this);

        $this->prefix     = $previousPrefix;
        $this->middleware = $previousMiddleware;
    }

    private function addRoute(string $method, string $path, string $handler): void
    {
        $fullPath = $this->prefix . $path;
        $this->routes[] = [
            'method'     => $method,
            'path'       => $fullPath,
            'handler'    => $handler,
            'middleware' => $this->middleware,
            'regex'      => $this->pathToRegex($fullPath),
            'params'     => $this->extractParamNames($fullPath),
        ];
    }

    private function pathToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function extractParamNames(string $path): array
    {
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $path, $matches);
        return $matches[1] ?? [];
    }

    public function dispatch(Request $request, Response $response): void
    {
        $method = $request->method();
        $uri    = $request->uri();

        // Support method override
        if ($method === 'POST') {
            $override = $_POST['_method'] ?? $request->input('_method');
            if ($override) {
                $method = strtoupper($override);
            }
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            if (!preg_match($route['regex'], $uri, $matches)) continue;

            // Extract route params
            $params = [];
            array_shift($matches);
            foreach ($route['params'] as $i => $name) {
                $params[$name] = $matches[$i] ?? null;
            }
            $request->setParams($params);

            // Resolve and call handler
            [$controllerClass, $action] = explode('@', $route['handler'], 2);

            // Determine full class name
            if (!str_contains($controllerClass, '\\')) {
                if (str_starts_with($uri, '/api/')) {
                    $controllerClass = "App\\Controllers\\Api\\V1\\$controllerClass";
                } else {
                    $controllerClass = "App\\Controllers\\Web\\$controllerClass";
                }
            }

            if (!class_exists($controllerClass)) {
                $response->error("Controller not found: $controllerClass", 500);
            }

            $controller = new $controllerClass();
            if (!method_exists($controller, $action)) {
                $response->error("Action not found: $action", 500);
            }

            $controller->$action($request, $response);
            return;
        }

        // Not found
        if (str_starts_with($uri, '/api/')) {
            $response->notFound("Endpoint not found: $method $uri");
        }

        http_response_code(404);
        View::render('layout/404', ['uri' => $uri]);
    }
}
