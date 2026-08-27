<?php

declare(strict_types=1);

namespace App\Core;

// Routeur simple : associe une methode HTTP + un chemin (avec parametres
// du type /cours/{id}) a une methode de controleur.
final class Router
{
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
        preg_match_all('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', $path, $matches);
        $keys = $matches[1];

        $regex = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $path);

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

        $this->afficher404();
    }

    private function afficher404(): void
    {
        http_response_code(404);

        ob_start();
        require dirname(__DIR__) . '/Views/errors/404.php';
        $content = ob_get_clean();

        require dirname(__DIR__) . '/Views/layouts/front.php';
    }
}
