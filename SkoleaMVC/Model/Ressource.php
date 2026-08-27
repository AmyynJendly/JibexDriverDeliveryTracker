<?php
class Ressource
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = config::getConnexion();
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ressources WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    // Ressource + infos du module/cours parents, pour verifier le proprietaire.
    public function findAvecCours($id)
    {
        $stmt = $this->pdo->prepare('
            SELECT r.*, m.cours_id, m.titre AS module_titre, c.formateur_id
            FROM ressources r
            INNER JOIN modules m ON m.id = r.module_id
            INNER JOIN cours c ON c.id = m.cours_id
            WHERE r.id = :id
        ');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function byModule($moduleId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ressources WHERE module_id = :module_id ORDER BY id ASC');
        $stmt->execute(['module_id' => $moduleId]);

        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO ressources (module_id, titre, type, contenu, description)
            VALUES (:module_id, :titre, :type, :contenu, :description)
        ');
        $stmt->execute([
            'module_id' => $data['module_id'],
            'titre' => $data['titre'],
            'type' => $data['type'],
            'contenu' => $data['contenu'],
            'description' => $data['description'] !== '' ? $data['description'] : null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = 'UPDATE ressources SET titre = :titre, type = :type, description = :description';
        $params = [
            'id' => $id,
            'titre' => $data['titre'],
            'type' => $data['type'],
            'description' => $data['description'] !== '' ? $data['description'] : null,
        ];

        if (!empty($data['contenu'])) {
            $sql .= ', contenu = :contenu';
            $params['contenu'] = $data['contenu'];
        }

        $sql .= ' WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM ressources WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
