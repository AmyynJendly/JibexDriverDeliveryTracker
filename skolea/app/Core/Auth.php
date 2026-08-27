<?php

declare(strict_types=1);

namespace App\Core;

// Gestion de la session utilisateur connecte (login, logout, role).
final class Auth
{
    public static function login(array $utilisateur): void
    {
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
        $_SESSION['utilisateur'] = [
            'id'     => (int) $utilisateur['id'],
            'nom'    => $utilisateur['nom'],
            'prenom' => $utilisateur['prenom'],
            'email'  => $utilisateur['email'],
            'role'   => $utilisateur['role'],
            'photo'  => $utilisateur['photo'] ?? null,
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION['utilisateur']);
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
    }

    public static function check(): bool
    {
        return isset($_SESSION['utilisateur']);
    }

    public static function user(): ?array
    {
        return $_SESSION['utilisateur'] ?? null;
    }

    public static function id(): ?int
    {
        return self::check() ? (int) $_SESSION['utilisateur']['id'] : null;
    }

    public static function role(): ?string
    {
        return self::check() ? $_SESSION['utilisateur']['role'] : null;
    }

    public static function hasRole(string ...$roles): bool
    {
        return self::check() && in_array(self::role(), $roles, true);
    }

    // Met a jour la session apres modification du profil, sans se reconnecter.
    public static function refresh(array $utilisateur): void
    {
        if (self::check()) {
            self::login($utilisateur);
        }
    }
}
