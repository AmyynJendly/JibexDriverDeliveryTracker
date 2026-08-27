<?php
class InscriptionController
{
    private $model;

    public function __construct()
    {
        $this->model = new Inscription();
    }

    // Inscrit l'etudiant si ce n'est pas deja fait (evite les doublons).
    public function inscrire($etudiantId, $coursId)
    {
        if (!$this->model->findByEtudiantEtCours($etudiantId, $coursId)) {
            $this->model->create($etudiantId, $coursId);
        }
    }

    public function desinscrire($etudiantId, $coursId)
    {
        $inscription = $this->model->findByEtudiantEtCours($etudiantId, $coursId);
        if ($inscription) {
            $this->model->delete($inscription['id']);
        }
    }

    public function toggleModule($etudiantId, $coursId, $moduleId)
    {
        $inscription = $this->model->trouverDetailleeParEtudiantEtCours($etudiantId, $coursId);
        if (!$inscription) {
            return;
        }

        $moduleModel = new Module();
        $totalModules = $moduleModel->countByCours($coursId);
        $this->model->basculerModule($inscription, $moduleId, $totalModules);
    }
}
