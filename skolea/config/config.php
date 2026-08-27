<?php

declare(strict_types=1);

// Configuration de l'application. Les identifiants de la base peuvent
// etre surcharges par des variables d'environnement.
return [
    'app_name' => 'Skolea',

    'db' => [
        'host'    => getenv('SKOLEA_DB_HOST') ?: '127.0.0.1',
        'name'    => getenv('SKOLEA_DB_NAME') ?: 'skolea',
        'user'    => getenv('SKOLEA_DB_USER') ?: 'skolea',
        'pass'    => getenv('SKOLEA_DB_PASS') ?: 'skolea',
        'charset' => 'utf8mb4',
    ],
];
