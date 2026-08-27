<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Cours
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    private const SELECT_AVEC_JOINTURES = "
        SELECT c.*, cat.nom AS categorie_nom,
               u.nom AS formateur_nom, u.prenom AS formateur_prenom,
               (SELECT COUNT(*) FROM inscriptions i WHERE i.cours_id = c.id) AS nb_inscrits,
               (SELECT COUNT(*) FROM modules m WHERE m.cours_id = c.id) AS nb_modules
        FROM cours c
        INNER JOIN categories cat ON cat.id = c.categorie_id
        INNER JOIN utilisateurs u ON u.id = c.formateur_id
    ";

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(self::SELECT_AVEC_JOINTURES . ' WHERE c.id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO cours (titre, description, categorie_id, formateur_id, image, niveau, statut)
            VALUES (:titre, :description, :categorie_id, :formateur_id, :image, :niveau, :statut)
        ');
        $stmt->execute([
            'titre' => $data['titre'],
            'description' => $data['description'],
            'categorie_id' => $data['categorie_id'],
            'formateur_id' => $data['formateur_id'],
            'image' => $data['image'] ?? null,
            'niveau' => $data['niveau'],
            'statut' => $data['statut'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = '
            UPDATE cours
            SET titre = :titre, description = :description, categorie_id = :categorie_id,
                niveau = :niveau, statut = :statut
        ';
        $params = [
            'id' => $id,
            'titre' => $data['titre'],
            'description' => $data['description'],
            'categorie_id' => $data['categorie_id'],
            'niveau' => $data['niveau'],
            'statut' => $data['statut'],
        ];

        if (!empty($data['image'])) {
            $sql .= ', image = :image';
            $params['image'] = $data['image'];
        }

        $sql .= ' WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM cours WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function appartientAuFormateur(int $coursId, int $formateurId): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM cours WHERE id = :id AND formateur_id = :formateur_id');
        $stmt->execute(['id' => $coursId, 'formateur_id' => $formateurId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Liste paginee pour le catalogue public et le back-office, avec filtres
     * optionnels par categorie, niveau, formateur, statut et recherche texte.
     */
    public function paginate(int $limit, int $offset, array $filtres = []): array
    {
        [$where, $params] = $this->buildFiltre($filtres);

        $stmt = $this->pdo->prepare(
            self::SELECT_AVEC_JOINTURES . " {$where} ORDER BY c.date_creation DESC LIMIT :limit OFFSET :offset"
        );

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function count(array $filtres = []): int
    {
        [$where, $params] = $this->buildFiltre($filtres);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM cours c {$where}");
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    private function buildFiltre(array $filtres): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filtres['statut'])) {
            $conditions[] = 'c.statut = :statut';
            $params[':statut'] = $filtres['statut'];
        }

        if (!empty($filtres['categorie_id'])) {
            $conditions[] = 'c.categorie_id = :categorie_id';
            $params[':categorie_id'] = $filtres['categorie_id'];
        }

        if (!empty($filtres['niveau'])) {
            $conditions[] = 'c.niveau = :niveau';
            $params[':niveau'] = $filtres['niveau'];
        }

        if (!empty($filtres['formateur_id'])) {
            $conditions[] = 'c.formateur_id = :formateur_id';
            $params[':formateur_id'] = $filtres['formateur_id'];
        }

        if (!empty($filtres['recherche'])) {
            $conditions[] = '(c.titre LIKE :recherche_titre OR c.description LIKE :recherche_desc)';
            $params[':recherche_titre'] = '%' . $filtres['recherche'] . '%';
            $params[':recherche_desc'] = '%' . $filtres['recherche'] . '%';
        }

        $where = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);

        return [$where, $params];
    }

    public function recents(int $limite = 3): array
    {
        $stmt = $this->pdo->prepare(
            self::SELECT_AVEC_JOINTURES . " WHERE c.statut = 'publie' ORDER BY c.date_creation DESC LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countByStatut(): array
    {
        return $this->pdo->query('SELECT statut, COUNT(*) AS total FROM cours GROUP BY statut')->fetchAll();
    }

    /**
     * Nombre de cours par categorie (cours publies uniquement), utilise pour
     * le graphique de repartition du tableau de bord administrateur.
     */
    public function repartitionParCategorie(): array
    {
        return $this->pdo->query("
            SELECT cat.nom AS categorie, COUNT(c.id) AS total
            FROM categories cat
            LEFT JOIN cours c ON c.categorie_id = cat.id AND c.statut = 'publie'
            GROUP BY cat.id
            ORDER BY total DESC
        ")->fetchAll();
    }
}
