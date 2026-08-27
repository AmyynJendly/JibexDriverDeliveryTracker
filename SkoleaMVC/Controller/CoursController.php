<?php
class CoursController
{
    private $model;

    public function __construct()
    {
        $this->model = new Cours();
    }

    // Retourne [validateur, id du cours cree ou null].
    public function creer($data, $fichierImage, $formateurId)
    {
        $validator = $this->valider($data);
        $image = null;

        if (!$validator->fails()) {
            try {
                $image = Upload::stocker($fichierImage, 'cours', ['jpg', 'jpeg', 'png', 'webp'], 3 * 1024 * 1024);
            } catch (RuntimeException $e) {
                $validator->addError('image', $e->getMessage());
            }
        }

        if ($validator->fails()) {
            return [$validator, null];
        }

        $id = $this->model->create([
            'titre' => $data['titre'],
            'description' => $data['description'],
            'categorie_id' => $data['categorie_id'],
            'formateur_id' => $formateurId,
            'image' => $image,
            'niveau' => $data['niveau'],
            'statut' => $data['statut'],
        ]);

        return [$validator, $id];
    }

    public function modifier($id, $data, $fichierImage)
    {
        $validator = $this->valider($data);
        $image = null;

        if (!$validator->fails()) {
            try {
                $image = Upload::stocker($fichierImage, 'cours', ['jpg', 'jpeg', 'png', 'webp'], 3 * 1024 * 1024);
            } catch (RuntimeException $e) {
                $validator->addError('image', $e->getMessage());
            }
        }

        if (!$validator->fails()) {
            $this->model->update($id, [
                'titre' => $data['titre'],
                'description' => $data['description'],
                'categorie_id' => $data['categorie_id'],
                'niveau' => $data['niveau'],
                'statut' => $data['statut'],
                'image' => $image,
            ]);
        }

        return $validator;
    }

    public function supprimer($id)
    {
        $this->model->delete($id);
    }

    // Affiche les informations d'un cours (objet Cours) passe en parametre.
    public function afficherCours($cours)
    {
        $cours->show();
    }

    public function appartientAuFormateur($coursId, $formateurId)
    {
        return $this->model->appartientAuFormateur($coursId, $formateurId);
    }

    private function valider($data)
    {
        $validator = new Validator($data);
        $validator
            ->required('titre', 'Titre')->min('titre', 3, 'Titre')->max('titre', 150, 'Titre')
            ->required('description', 'Description')
            ->required('categorie_id', 'Categorie')->numeric('categorie_id', 'Categorie')
            ->required('niveau', 'Niveau')->in('niveau', ['debutant', 'intermediaire', 'avance'], 'Niveau')
            ->required('statut', 'Statut')->in('statut', ['brouillon', 'publie'], 'Statut');

        if (!$validator->fails()) {
            $categorieModel = new Categorie();
            if (!$categorieModel->find((int) $data['categorie_id'])) {
                $validator->addError('categorie_id', 'Categorie invalide.');
            }
        }

        return $validator;
    }
}
