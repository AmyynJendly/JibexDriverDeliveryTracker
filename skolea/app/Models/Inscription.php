<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Inscription
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM inscriptions WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function findByEtudiantEtCours(int $etudiantId, int $coursId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM inscriptions WHERE etudiant_id = :etudiant_id AND cours_id = :cours_id');
        $stmt->execute(['etudiant_id' => $etudiantId, 'cours_id' => $coursId]);

        return $stmt->fetch() ?: null;
    }

    public function create(int $etudiantId, int $coursId): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO inscriptions (etudiant_id, cours_id) VALUES (:etudiant_id, :cours_id)');
        $stmt->execute(['etudiant_id' => $etudiantId, 'cours_id' => $coursId]);

        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM inscriptions WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Cours suivis par un etudiant, avec les informations du cours et du
     * formateur (jointure sur cours, categories et utilisateurs).
     */
    public function paginateByEtudiant(int $etudiantId, int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare('
            SELECT i.*, c.titre AS cours_titre, c.image AS cours_image, c.niveau,
                   cat.nom AS categorie_nom, u.nom AS formateur_nom, u.prenom AS formateur_prenom,
                   (SELECT COUNT(*) FROM modules m WHERE m.cours_id = c.id) AS nb_modules
            FROM inscriptions i
            INNER JOIN cours c ON c.id = i.cours_id
            INNER JOIN categories cat ON cat.id = c.categorie_id
            INNER JOIN utilisateurs u ON u.id = c.formateur_id
            WHERE i.etudiant_id = :etudiant_id
            ORDER BY i.date_inscription DESC
            LIMIT :limit OFFSET :offset
        ');
        $stmt->bindValue(':etudiant_id', $etudiantId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countByEtudiant(int $etudiantId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM inscriptions WHERE etudiant_id = :etudiant_id');
        $stmt->execute(['etudiant_id' => $etudiantId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Etudiants inscrits a un cours, pour la vue "participants" du formateur.
     */
    public function byCours(int $coursId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT i.*, u.nom, u.prenom, u.email
            FROM inscriptions i
            INNER JOIN utilisateurs u ON u.id = i.etudiant_id
            WHERE i.cours_id = :cours_id
            ORDER BY i.date_inscription DESC
        ');
        $stmt->execute(['cours_id' => $coursId]);

        return $stmt->fetchAll();
    }

    public function countByCours(int $coursId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM inscriptions WHERE cours_id = :cours_id');
        $stmt->execute(['cours_id' => $coursId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Bascule l'etat "termine" d'un module pour une inscription et recalcule
     * automatiquement la progression (%) et le statut de l'inscription.
     */
    public function basculerModule(array $inscription, int $moduleId, int $totalModules): void
    {
        $termines = $inscription['modules_termines'] !== null && $inscription['modules_termines'] !== ''
            ? array_map('intval', explode(',', $inscription['modules_termines']))
            : [];

        if (in_array($moduleId, $termines, true)) {
            $termines = array_values(array_diff($termines, [$moduleId]));
        } else {
            $termines[] = $moduleId;
        }

        $progression = $totalModules > 0 ? (int) round((count($termines) / $totalModules) * 100) : 0;
        $statut = match (true) {
            $progression >= 100 => 'termine',
            default => 'en_cours',
        };

        $stmt = $this->pdo->prepare('
            UPDATE inscriptions
            SET modules_termines = :modules_termines, progression = :progression, statut = :statut
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $inscription['id'],
            'modules_termines' => $termines === [] ? null : implode(',', $termines),
            'progression' => $progression,
            'statut' => $statut,
        ]);
    }

    public function countByStatut(): array
    {
        return $this->pdo->query('SELECT statut, COUNT(*) AS total FROM inscriptions GROUP BY statut')->fetchAll();
    }

    /**
     * Nombre d'inscriptions par cours pour un formateur donne, utilise sur
     * son tableau de statistiques.
     */
    public function repartitionParCoursPourFormateur(int $formateurId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT c.titre, COUNT(i.id) AS total
            FROM cours c
            LEFT JOIN inscriptions i ON i.cours_id = c.id
            WHERE c.formateur_id = :formateur_id
            GROUP BY c.id
            ORDER BY total DESC
        ');
        $stmt->execute(['formateur_id' => $formateurId]);

        return $stmt->fetchAll();
    }
}
