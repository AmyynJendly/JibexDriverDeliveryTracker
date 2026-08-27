<?php
class RessourceController
{
    private $model;

    public function __construct()
    {
        $this->model = new Ressource();
    }

    public function creer($moduleId, $data, $fichier)
    {
        list($validator, $contenu) = $this->valider($data, $fichier, true);

        if (!$validator->fails()) {
            $this->model->create([
                'module_id' => $moduleId,
                'titre' => $data['titre'],
                'type' => $data['type'],
                'contenu' => $contenu,
                'description' => $data['description'],
            ]);
        }

        return $validator;
    }

    public function modifier($id, $data, $fichier)
    {
        list($validator, $contenu) = $this->valider($data, $fichier, false);

        if (!$validator->fails()) {
            $this->model->update($id, [
                'titre' => $data['titre'],
                'type' => $data['type'],
                'contenu' => $contenu,
                'description' => $data['description'],
            ]);
        }

        return $validator;
    }

    public function supprimer($id)
    {
        $this->model->delete($id);
    }

    // Retourne la ressource + infos du cours parent, ou null si elle
    // n'existe pas ou n'appartient pas a ce formateur.
    public function trouverPourFormateur($id, $formateurId)
    {
        $ressource = $this->model->findAvecCours($id);

        if (!$ressource || (int) $ressource['formateur_id'] !== (int) $formateurId) {
            return null;
        }

        return $ressource;
    }

    private function valider($data, $fichier, $obligatoire)
    {
        $validator = new Validator($data);
        $validator
            ->required('titre', 'Titre')->max('titre', 150, 'Titre')
            ->required('type', 'Type')->in('type', ['document', 'video', 'quiz'], 'Type');

        $contenu = $data['contenu'] !== '' ? $data['contenu'] : null;

        if ($data['type'] === 'document') {
            try {
                $fichierStocke = Upload::stocker($fichier, 'ressources', ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'csv'], 8 * 1024 * 1024);
                if ($fichierStocke !== null) {
                    $contenu = $fichierStocke;
                }
            } catch (RuntimeException $e) {
                $validator->addError('fichier', $e->getMessage());
            }
        }

        if ($obligatoire && $contenu === null) {
            $validator->addError('contenu', 'Fournissez un fichier ou une URL pour cette ressource.');
        }

        return [$validator, $contenu];
    }
}
