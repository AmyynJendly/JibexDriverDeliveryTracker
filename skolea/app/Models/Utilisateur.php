<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

// Acces aux donnees de la table utilisateurs.
final class Utilisateur
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateurs WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateurs WHERE email = :email');
        $stmt->execute(['email' => $email]);

        return $stmt->fetch() ?: null;
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM utilisateurs WHERE email = :email';
        $params = ['email' => $email];

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
        $stmt = $this->pdo->prepare('
            INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, bio)
            VALUES (:nom, :prenom, :email, :mot_de_passe, :role, :bio)
        ');
        $stmt->execute([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'mot_de_passe' => $data['mot_de_passe'],
            'role' => $data['role'] ?? 'etudiant',
            'bio' => $data['bio'] ?? null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE utilisateurs
            SET nom = :nom, prenom = :prenom, email = :email, role = :role, bio = :bio
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $id,
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'role' => $data['role'],
            'bio' => $data['bio'] ?? null,
        ]);
    }

    public function updateProfil(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('UPDATE utilisateurs SET nom = :nom, prenom = :prenom, bio = :bio WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'bio' => $data['bio'] ?? null,
        ]);
    }

    public function updatePassword(int $id, string $motDePasseHash): void
    {
        $stmt = $this->pdo->prepare('UPDATE utilisateurs SET mot_de_passe = :mdp WHERE id = :id');
        $stmt->execute(['id' => $id, 'mdp' => $motDePasseHash]);
    }

    public function updatePhoto(int $id, string $photo): void
    {
        $stmt = $this->pdo->prepare('UPDATE utilisateurs SET photo = :photo WHERE id = :id');
        $stmt->execute(['id' => $id, 'photo' => $photo]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM utilisateurs WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    // Liste paginee, avec filtre optionnel par role et recherche nom/prenom/email.
    public function paginate(int $limit, int $offset, ?string $role = null, ?string $recherche = null): array
    {
        [$where, $params] = $this->buildFiltre($role, $recherche);

        $stmt = $this->pdo->prepare("
            SELECT * FROM utilisateurs
            {$where}
            ORDER BY date_creation DESC
            LIMIT :limit OFFSET :offset
        ");

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function count(?string $role = null, ?string $recherche = null): int
    {
        [$where, $params] = $this->buildFiltre($role, $recherche);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM utilisateurs {$where}");
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    private function buildFiltre(?string $role, ?string $recherche): array
    {
        $conditions = [];
        $params = [];

        if ($role !== null && $role !== '') {
            $conditions[] = 'role = :role';
            $params[':role'] = $role;
        }

        if ($recherche !== null && $recherche !== '') {
            $conditions[] = '(nom LIKE :recherche_nom OR prenom LIKE :recherche_prenom OR email LIKE :recherche_email)';
            $params[':recherche_nom'] = '%' . $recherche . '%';
            $params[':recherche_prenom'] = '%' . $recherche . '%';
            $params[':recherche_email'] = '%' . $recherche . '%';
        }

        $where = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);

        return [$where, $params];
    }

    public function countByRole(string $role): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM utilisateurs WHERE role = :role');
        $stmt->execute(['role' => $role]);

        return (int) $stmt->fetchColumn();
    }

    // Liste des formateurs, pour remplir les listes deroulantes.
    public function formateurs(): array
    {
        return $this->pdo->query("SELECT id, nom, prenom FROM utilisateurs WHERE role = 'formateur' ORDER BY nom")->fetchAll();
    }

    // Nombre de comptes par role, pour le tableau de bord admin.
    public function repartitionParRole(): array
    {
        return $this->pdo->query('SELECT role, COUNT(*) AS total FROM utilisateurs GROUP BY role')->fetchAll();
    }

    public function inscriptionsParMois(int $mois = 6): array
    {
        $stmt = $this->pdo->prepare("
            SELECT DATE_FORMAT(date_creation, '%Y-%m') AS mois, COUNT(*) AS total
            FROM utilisateurs
            WHERE date_creation >= DATE_SUB(CURDATE(), INTERVAL :mois MONTH)
            GROUP BY mois
            ORDER BY mois
        ");
        $stmt->bindValue(':mois', $mois, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
