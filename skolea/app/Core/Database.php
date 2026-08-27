<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

// Connexion PDO unique a la base de donnees (pas de MySQLi).
final class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require dirname(__DIR__, 2) . '/config/config.php';
            $db = $config['db'];

            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $db['host'],
                $db['name'],
                $db['charset']
            );

            try {
                self::$instance = new PDO($dsn, $db['user'], $db['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                throw new RuntimeException(
                    'Impossible de se connecter a la base de donnees. Verifiez la configuration dans config/config.php.',
                    (int) $e->getCode(),
                    $e
                );
            }
        }

        return self::$instance;
    }
}
