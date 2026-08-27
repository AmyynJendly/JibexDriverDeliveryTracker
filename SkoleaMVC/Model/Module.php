<?php
class Module
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = config::getConnexion();
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM modules WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    // Module + infos du cours parent, pour verifier que le formateur en est proprietaire.
    public function findAvecCours($id)
    {
        $stmt = $this->pdo->prepare('
            SELECT m.*, c.formateur_id, c.titre AS cours_titre
            FROM modules m
            INNER JOIN cours c ON c.id = m.cours_id
            WHERE m.id = :id
        ');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function byCours($coursId)
    {
        $stmt = $this->pdo->prepare('
            SELECT m.*, (SELECT COUNT(*) FROM ressources r WHERE r.module_id = m.id) AS nb_ressources
            FROM modules m
            WHERE m.cours_id = :cours_id
            ORDER BY m.ordre ASC, m.id ASC
        ');
        $stmt->execute(['cours_id' => $coursId]);

        return $stmt->fetchAll();
    }

    public function countByCours($coursId)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM modules WHERE cours_id = :cours_id');
        $stmt->execute(['cours_id' => $coursId]);

        return (int) $stmt->fetchColumn();
    }

    public function prochainOrdre($coursId)
    {
        $stmt = $this->pdo->prepare('SELECT COALESCE(MAX(ordre), 0) + 1 FROM modules WHERE cours_id = :cours_id');
        $stmt->execute(['cours_id' => $coursId]);

        return (int) $stmt->fetchColumn();
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO modules (cours_id, titre, description, ordre)
            VALUES (:cours_id, :titre, :description, :ordre)
        ');
        $stmt->execute([
            'cours_id' => $data['cours_id'],
            'titre' => $data['titre'],
            'description' => $data['description'] !== '' ? $data['description'] : null,
            'ordre' => $data['ordre'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare('
            UPDATE modules SET titre = :titre, description = :description, ordre = :ordre WHERE id = :id
        ');
        $stmt->execute([
            'id' => $id,
            'titre' => $data['titre'],
            'description' => $data['description'] !== '' ? $data['description'] : null,
            'ordre' => $data['ordre'],
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM modules WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
