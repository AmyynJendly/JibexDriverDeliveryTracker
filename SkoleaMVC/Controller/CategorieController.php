<?php
class CategorieController
{
    private $model;

    public function __construct()
    {
        $this->model = new Categorie();
    }

    public function creer($data)
    {
        $validator = $this->valider($data, null);

        if (!$validator->fails()) {
            $this->model->create($data);
        }

        return $validator;
    }

    public function modifier($id, $data)
    {
        $validator = $this->valider($data, $id);

        if (!$validator->fails()) {
            $this->model->update($id, $data);
        }

        return $validator;
    }

    // Retourne un message d'erreur (string) en cas d'echec, ou null si la suppression a reussi.
    public function supprimer($id)
    {
        if ($this->model->countCours($id) > 0) {
            return 'Impossible de supprimer : des cours sont encore rattaches a cette categorie.';
        }

        try {
            $this->model->delete($id);
            return null;
        } catch (PDOException $e) {
            return 'Impossible de supprimer cette categorie.';
        }
    }

    private function valider($data, $idActuel)
    {
        $validator = new Validator($data);
        $validator->required('nom', 'Nom')->min('nom', 3, 'Nom')->max('nom', 100, 'Nom')->max('description', 255, 'Description');

        if (!$validator->fails() && $this->model->nomExists($data['nom'], $idActuel)) {
            $validator->addError('nom', 'Une categorie porte deja ce nom.');
        }

        return $validator;
    }
}
