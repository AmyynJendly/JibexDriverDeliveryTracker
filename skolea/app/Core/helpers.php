<?php

declare(strict_types=1);

use App\Core\Auth;

// Petites fonctions utilitaires utilisees dans les vues et les controleurs.

function base_path(): string
{
    static $base = null;

    if ($base === null) {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $base = rtrim($scriptDir, '/');
    }

    return $base;
}

function url(string $path = '/'): string
{
    $path = '/' . ltrim($path, '/');

    return base_path() . $path;
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function uploads_url(string $path): string
{
    return url('uploads/' . ltrim($path, '/'));
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function old(array $old, string $key, string $default = ''): string
{
    return e((string) ($old[$key] ?? $default));
}

function flash_set(string $type, string $message): void
{
    $_SESSION['_flash'][$type] = $message;
}

function flash_get(): array
{
    $flash = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);

    return $flash;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_verify(string $token): bool
{
    return $token !== '' && isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function format_date(?string $datetime, string $format = 'd/m/Y'): string
{
    if (!$datetime) {
        return '';
    }

    try {
        return (new DateTime($datetime))->format($format);
    } catch (Exception) {
        return '';
    }
}

function role_label(string $role): string
{
    return match ($role) {
        'administrateur' => 'Administrateur',
        'formateur' => 'Formateur',
        'etudiant' => 'Etudiant',
        default => ucfirst($role),
    };
}

function niveau_label(string $niveau): string
{
    return match ($niveau) {
        'debutant' => 'Debutant',
        'intermediaire' => 'Intermediaire',
        'avance' => 'Avance',
        default => ucfirst($niveau),
    };
}

function type_ressource_label(string $type): string
{
    return match ($type) {
        'document' => 'Document',
        'video' => 'Video',
        'quiz' => 'Quiz',
        default => ucfirst($type),
    };
}

function current_user(): ?array
{
    return Auth::user();
}

// Reconstruit l'URL courante (?a=1&b=2) en gardant les parametres actuels
// et en ecrasant ceux passes en argument. Utilise pour les liens de pagination.
function query_with(array $overrides): string
{
    $params = array_merge($_GET, $overrides);

    $propres = [];
    foreach ($params as $cle => $valeur) {
        if ($valeur !== null && $valeur !== '') {
            $propres[$cle] = $valeur;
        }
    }

    return '?' . http_build_query($propres);
}
