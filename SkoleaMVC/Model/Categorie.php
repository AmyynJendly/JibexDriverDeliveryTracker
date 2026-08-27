<?php
class Categorie
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = config::getConnexion();
    }

    public function all()
    {
        return $this->pdo->query('SELECT * FROM categories ORDER BY nom')->fetchAll();
    }

    // Toutes les categories avec le nombre de cours rattaches a chacune.
    public function allWithCoursCount()
    {
        return $this->pdo->query('
            SELECT cat.*, COUNT(c.id) AS nb_cours
            FROM categories cat
            LEFT JOIN cours c ON c.categorie_id = cat.id
            GROUP BY cat.id
            ORDER BY cat.nom
        ')->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function nomExists($nom, $excludeId = null)
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

    public function create($data)
    {
        $stmt = $this->pdo->prepare('INSERT INTO categories (nom, description) VALUES (:nom, :description)');
        $stmt->execute([
            'nom' => $data['nom'],
            'description' => $data['description'] !== '' ? $data['description'] : null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare('UPDATE categories SET nom = :nom, description = :description WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'nom' => $data['nom'],
            'description' => $data['description'] !== '' ? $data['description'] : null,
        ]);
    }

    // Echoue si des cours utilisent encore cette categorie (contrainte de la base).
    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function countCours($id)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM cours WHERE categorie_id = :id');
        $stmt->execute(['id' => $id]);

        return (int) $stmt->fetchColumn();
    }
}
