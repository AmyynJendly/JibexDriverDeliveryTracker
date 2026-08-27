<?php

declare(strict_types=1);

/**
 * Point d'entree unique de l'application (front controller).
 * Toutes les requetes sont redirigees ici par le fichier .htaccess.
 */

session_start();

define('APP_ROOT', dirname(__DIR__));

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = APP_ROOT . '/app/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

require APP_ROOT . '/app/Core/helpers.php';

$router = new App\Core\Router();
require APP_ROOT . '/app/routes.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'] ?? '/');
