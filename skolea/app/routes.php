<?php

declare(strict_types=1);

/**
 * Table de routage de l'application.
 * @var App\Core\Router $router
 */

use App\Controllers\AuthController;
use App\Controllers\SiteController;

// Les routes sont enregistrees au fur et a mesure que les controleurs
// sont developpes (site public, authentification, back-office, ...).

$router->get('/', [SiteController::class, 'home']);
$router->get('/a-propos', [SiteController::class, 'about']);

$router->get('/connexion', [AuthController::class, 'showLogin']);
$router->post('/connexion', [AuthController::class, 'login']);
$router->get('/inscription', [AuthController::class, 'showRegister']);
$router->post('/inscription', [AuthController::class, 'register']);
$router->post('/deconnexion', [AuthController::class, 'logout']);
