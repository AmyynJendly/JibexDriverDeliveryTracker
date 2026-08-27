<?php
// Petites fonctions utilisees dans les vues et les controleurs.
// Pas de namespace, pas d'autoload : ce fichier est simplement inclus
// avec require_once par chaque page qui en a besoin.

function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function old($old, $key, $default = '')
{
    return e($old[$key] ?? $default);
}

function flash_set($type, $message)
{
    $_SESSION['_flash'][$type] = $message;
}

function flash_get()
{
    $flash = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);

    return $flash;
}

function csrf_token()
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_verify($token)
{
    return $token !== '' && isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}

function csrf_field()
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function format_date($datetime, $format = 'd/m/Y')
{
    if (!$datetime) {
        return '';
    }

    try {
        return (new DateTime($datetime))->format($format);
    } catch (Exception $e) {
        return '';
    }
}

function role_label($role)
{
    if ($role === 'administrateur') return 'Administrateur';
    if ($role === 'formateur') return 'Formateur';
    if ($role === 'etudiant') return 'Etudiant';

    return ucfirst($role);
}

function niveau_label($niveau)
{
    if ($niveau === 'debutant') return 'Debutant';
    if ($niveau === 'intermediaire') return 'Intermediaire';
    if ($niveau === 'avance') return 'Avance';

    return ucfirst($niveau);
}

function type_ressource_label($type)
{
    if ($type === 'document') return 'Document';
    if ($type === 'video') return 'Video';
    if ($type === 'quiz') return 'Quiz';

    return ucfirst($type);
}

// --- Session utilisateur ---

function utilisateur_connecte()
{
    return $_SESSION['utilisateur'] ?? null;
}

function est_connecte()
{
    return isset($_SESSION['utilisateur']);
}

function a_le_role(...$roles)
{
    return est_connecte() && in_array($_SESSION['utilisateur']['role'], $roles, true);
}
