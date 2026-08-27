<?php
class Cours
{
    private $pdo;

    private $selectAvecJointures = "
        SELECT c.*, cat.nom AS categorie_nom,
               u.nom AS formateur_nom, u.prenom AS formateur_prenom,
               (SELECT COUNT(*) FROM inscriptions i WHERE i.cours_id = c.id) AS nb_inscrits,
               (SELECT COUNT(*) FROM modules m WHERE m.cours_id = c.id) AS nb_modules
        FROM cours c
        INNER JOIN categories cat ON cat.id = c.categorie_id
        INNER JOIN utilisateurs u ON u.id = c.formateur_id
    ";

    // Attributs prives utilises pour representer UN cours en memoire
    // (constructeur + accesseurs), independamment des methodes d'acces
    // a la base de donnees ci-dessous.
    private $titre;
    private $description;
    private $categorieId;
    private $niveau;
    private $statut;

    public function __construct($titre = null, $description = null, $categorieId = null, $niveau = null, $statut = null)
    {
        $this->pdo = config::getConnexion();
        $this->titre = $titre;
        $this->description = $description;
        $this->categorieId = $categorieId;
        $this->niveau = $niveau;
        $this->statut = $statut;
    }

    public function getTitre() { return $this->titre; }
    public function setTitre($titre) { $this->titre = $titre; }

    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; }

    public function getCategorieId() { return $this->categorieId; }
    public function setCategorieId($categorieId) { $this->categorieId = $categorieId; }

    public function getNiveau() { return $this->niveau; }
    public function setNiveau($niveau) { $this->niveau = $niveau; }

    public function getStatut() { return $this->statut; }
    public function setStatut($statut) { $this->statut = $statut; }

    // Affiche les informations de CE cours dans un tableau HTML.
    public function show()
    {
        echo '<table class="table"><tbody>';
        echo '<tr><th>Titre</th><td>' . htmlspecialchars((string) $this->titre) . '</td></tr>';
        echo '<tr><th>Description</th><td>' . htmlspecialchars((string) $this->description) . '</td></tr>';
        echo '<tr><th>Categorie (id)</th><td>' . htmlspecialchars((string) $this->categorieId) . '</td></tr>';
        echo '<tr><th>Niveau</th><td>' . htmlspecialchars((string) $this->niveau) . '</td></tr>';
        echo '<tr><th>Statut</th><td>' . htmlspecialchars((string) $this->statut) . '</td></tr>';
        echo '</tbody></table>';
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare($this->selectAvecJointures . ' WHERE c.id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function create($data)
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

    public function update($id, $data)
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

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM cours WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function appartientAuFormateur($coursId, $formateurId)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM cours WHERE id = :id AND formateur_id = :formateur_id');
        $stmt->execute(['id' => $coursId, 'formateur_id' => $formateurId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    // Liste paginee avec filtres optionnels (categorie, niveau, formateur, statut, recherche).
    public function paginate($limit, $offset, $filtres = [])
    {
        list($where, $params) = $this->buildFiltre($filtres);

        $stmt = $this->pdo->prepare(
            $this->selectAvecJointures . " {$where} ORDER BY c.date_creation DESC LIMIT :limit OFFSET :offset"
        );

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function count($filtres = [])
    {
        list($where, $params) = $this->buildFiltre($filtres);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM cours c {$where}");
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    private function buildFiltre($filtres)
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

    public function recents($limite = 3)
    {
        $stmt = $this->pdo->prepare(
            $this->selectAvecJointures . " WHERE c.statut = 'publie' ORDER BY c.date_creation DESC LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countByStatut()
    {
        return $this->pdo->query('SELECT statut, COUNT(*) AS total FROM cours GROUP BY statut')->fetchAll();
    }

    // Nombre de cours publies par categorie, pour le tableau de bord.
    public function repartitionParCategorie()
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
