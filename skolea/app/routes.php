<?php

declare(strict_types=1);

/**
 * Table de routage de l'application.
 * @var App\Core\Router $router
 */

use App\Controllers\Admin\CategorieController as AdminCategorieController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\UtilisateurController as AdminUtilisateurController;
use App\Controllers\AuthController;
use App\Controllers\ProfilController;
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

$router->get('/profil', [ProfilController::class, 'show']);
$router->post('/profil', [ProfilController::class, 'update']);
$router->post('/profil/mot-de-passe', [ProfilController::class, 'updatePassword']);

// --- Back-office administrateur ---
$router->get('/admin', [AdminDashboardController::class, 'index']);
$router->get('/admin/statistiques', [AdminDashboardController::class, 'statistiques']);

$router->get('/admin/utilisateurs', [AdminUtilisateurController::class, 'index']);
$router->get('/admin/utilisateurs/creer', [AdminUtilisateurController::class, 'create']);
$router->post('/admin/utilisateurs/creer', [AdminUtilisateurController::class, 'store']);
$router->get('/admin/utilisateurs/{id}/modifier', [AdminUtilisateurController::class, 'edit']);
$router->post('/admin/utilisateurs/{id}/modifier', [AdminUtilisateurController::class, 'update']);
$router->post('/admin/utilisateurs/{id}/supprimer', [AdminUtilisateurController::class, 'destroy']);

$router->get('/admin/categories', [AdminCategorieController::class, 'index']);
$router->get('/admin/categories/creer', [AdminCategorieController::class, 'create']);
$router->post('/admin/categories/creer', [AdminCategorieController::class, 'store']);
$router->get('/admin/categories/{id}/modifier', [AdminCategorieController::class, 'edit']);
$router->post('/admin/categories/{id}/modifier', [AdminCategorieController::class, 'update']);
$router->post('/admin/categories/{id}/supprimer', [AdminCategorieController::class, 'destroy']);
