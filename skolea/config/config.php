<?php

declare(strict_types=1);

/**
 * Configuration centrale de l'application.
 * Les valeurs peuvent etre surchargees par des variables d'environnement,
 * ce qui evite de coder en dur des identifiants sensibles.
 */
return [
    'app_name' => 'Skolea',
    'app_locale' => 'fr',

    'db' => [
        'host'    => getenv('SKOLEA_DB_HOST') ?: '127.0.0.1',
        'name'    => getenv('SKOLEA_DB_NAME') ?: 'skolea',
        'user'    => getenv('SKOLEA_DB_USER') ?: 'skolea',
        'pass'    => getenv('SKOLEA_DB_PASS') ?: 'skolea',
        'charset' => 'utf8mb4',
    ],

    // Nombre d'elements affiches par page dans les listes paginees.
    'pagination' => [
        'cours_par_page' => 6,
        'utilisateurs_par_page' => 8,
    ],
];
