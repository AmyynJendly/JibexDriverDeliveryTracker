<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Categorie
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM categories ORDER BY nom')->fetchAll();
    }

    // Toutes les categories avec le nombre de cours rattaches a chacune.
    public function allWithCoursCount(): array
    {
        return $this->pdo->query('
            SELECT cat.*, COUNT(c.id) AS nb_cours
            FROM categories cat
            LEFT JOIN cours c ON c.categorie_id = cat.id
            GROUP BY cat.id
            ORDER BY cat.nom
        ')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function nomExists(string $nom, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM categories WHERE nom = :nom';
        $params = ['nom' => $nom];

        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO categories (nom, description) VALUES (:nom, :description)');
        $stmt->execute([
            'nom' => $data['nom'],
            'description' => $data['description'] !== '' ? $data['description'] : null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('UPDATE categories SET nom = :nom, description = :description WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'nom' => $data['nom'],
            'description' => $data['description'] !== '' ? $data['description'] : null,
        ]);
    }

    // Echoue si des cours utilisent encore cette categorie (contrainte de la base).
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function countCours(int $id): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM cours WHERE categorie_id = :id');
        $stmt->execute(['id' => $id]);

        return (int) $stmt->fetchColumn();
    }
}
