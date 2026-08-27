<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Classe de base heritee par tous les controleurs de l'application.
 * Regroupe le rendu des vues, les redirections et les controles d'acces.
 */
abstract class Controller
{
    /**
     * Affiche une vue dans une mise en page (layout).
     *
     * @param string      $view   chemin de la vue, relatif a app/Views, sans extension
     * @param array       $data   variables rendues disponibles dans la vue
     * @param string|null $layout 'front', 'back', ou null pour un rendu sans layout
     */
    protected function view(string $view, array $data = [], ?string $layout = 'front'): void
    {
        $viewFile = dirname(__DIR__) . '/Views/' . $view . '.php';

        if (!is_file($viewFile)) {
            throw new \RuntimeException("Vue introuvable : {$view}");
        }

        extract($data, EXTR_SKIP);

        if ($layout === null) {
            require $viewFile;

            return;
        }

        $layoutFile = dirname(__DIR__) . "/Views/layouts/{$layout}.php";

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require $layoutFile;
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function back(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        header('Location: ' . $referer);
        exit;
    }

    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;

        return is_string($value) ? trim($value) : $value;
    }

    /**
     * Interrompt l'action si l'utilisateur n'est pas connecte.
     */
    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            flash_set('erreur', 'Vous devez etre connecte pour acceder a cette page.');
            $this->redirect('/connexion');
        }
    }

    /**
     * Interrompt l'action si l'utilisateur n'a pas l'un des roles autorises.
     */
    protected function requireRole(string ...$roles): void
    {
        $this->requireAuth();

        if (!Auth::hasRole(...$roles)) {
            http_response_code(403);
            $this->view('errors/403', [], 'front');
            exit;
        }
    }

    /**
     * Verifie le jeton CSRF envoye par un formulaire POST.
     */
    protected function verifyCsrf(): void
    {
        $token = (string) $this->input('_csrf', '');

        if (!csrf_verify($token)) {
            http_response_code(419);
            $this->view('errors/403', ['message' => 'Session expiree, merci de reessayer.'], 'front');
            exit;
        }
    }
}
