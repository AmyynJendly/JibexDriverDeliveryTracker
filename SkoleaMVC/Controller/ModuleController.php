<?php
class ModuleController
{
    private $model;

    public function __construct()
    {
        $this->model = new Module();
    }

    public function creer($coursId, $data)
    {
        $validator = $this->valider($data);

        if (!$validator->fails()) {
            $ordre = $data['ordre'] !== '' ? (int) $data['ordre'] : $this->model->prochainOrdre($coursId);
            $this->model->create([
                'cours_id' => $coursId,
                'titre' => $data['titre'],
                'description' => $data['description'],
                'ordre' => $ordre,
            ]);
        }

        return $validator;
    }

    public function modifier($id, $data)
    {
        $validator = $this->valider($data);

        if (!$validator->fails()) {
            $module = $this->model->find($id);
            $ordre = $data['ordre'] !== '' ? (int) $data['ordre'] : (int) $module['ordre'];
            $this->model->update($id, [
                'titre' => $data['titre'],
                'description' => $data['description'],
                'ordre' => $ordre,
            ]);
        }

        return $validator;
    }

    public function supprimer($id)
    {
        $this->model->delete($id);
    }

    // Retourne le module + infos du cours parent, ou null s'il n'existe pas
    // ou n'appartient pas a ce formateur.
    public function trouverPourFormateur($id, $formateurId)
    {
        $module = $this->model->findAvecCours($id);

        if (!$module || (int) $module['formateur_id'] !== (int) $formateurId) {
            return null;
        }

        return $module;
    }

    private function valider($data)
    {
        $validator = new Validator($data);
        $validator->required('titre', 'Titre')->min('titre', 3, 'Titre')->max('titre', 150, 'Titre');

        if ($data['ordre'] !== '') {
            $validator->numeric('ordre', 'Ordre');
        }

        return $validator;
    }
}
