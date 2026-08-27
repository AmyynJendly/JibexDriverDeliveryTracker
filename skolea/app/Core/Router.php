<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Routeur minimaliste : associe une methode HTTP + un chemin
 * (avec parametres dynamiques du type /cours/{id}) a une action
 * de controleur, sans dependre d'un framework externe.
 */
final class Router
{
    /** @var array<int, array{method: string, pattern: string, keys: array<int, string>, handler: array{0: class-string, 1: string}}> */
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, array $handler): void
    {
        $keys = [];
        $regex = preg_replace_callback('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', function ($matches) use (&$keys) {
            $keys[] = $matches[1];

            return '([^/]+)';
        }, $path);

        $this->routes[] = [
            'method'  => $method,
            'pattern' => '#^' . rtrim((string) $regex, '/') . '/?$#',
            'keys'    => $keys,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $base = base_path();

        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        $path = '/' . ltrim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches)) {
                array_shift($matches);
                $params = array_combine($route['keys'], $matches) ?: [];

                [$controllerClass, $action] = $route['handler'];
                $controller = new $controllerClass();
                call_user_func_array([$controller, $action], $params);

                return;
            }
        }

        http_response_code(404);
        (new class extends Controller {
            public function notFound(): void
            {
                $this->view('errors/404', [], 'front');
            }
        })->notFound();
    }
}
