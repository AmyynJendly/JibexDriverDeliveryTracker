<?php

declare(strict_types=1);

namespace App\Controllers\Formateur;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Cours;
use App\Models\Inscription;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireRole('formateur');

        $formateurId = (int) Auth::id();
        $coursModel = new Cours();
        $inscriptionModel = new Inscription();

        $cours = $coursModel->paginate(5, 0, ['formateur_id' => $formateurId]);

        $nbInscriptionsTotal = 0;
        foreach ($cours as $c) {
            $nbInscriptionsTotal += (int) $c['nb_inscrits'];
        }

        $repartitionInscriptions = [];
        foreach ($inscriptionModel->repartitionParCoursPourFormateur($formateurId) as $ligne) {
            $repartitionInscriptions[] = ['label' => $ligne['titre'], 'value' => (int) $ligne['total']];
        }

        $this->view('formateur/dashboard', [
            'title' => 'Tableau de bord',
            'nbCours' => $coursModel->count(['formateur_id' => $formateurId]),
            'nbCoursPublies' => $coursModel->count(['formateur_id' => $formateurId, 'statut' => 'publie']),
            'nbCoursBrouillon' => $coursModel->count(['formateur_id' => $formateurId, 'statut' => 'brouillon']),
            'nbInscriptionsTotal' => $nbInscriptionsTotal,
            'derniersCours' => $cours,
            'repartitionInscriptions' => $repartitionInscriptions,
        ], 'back');
    }

    public function statistiques(): void
    {
        $this->requireRole('formateur');

        $formateurId = (int) Auth::id();
        $inscriptionModel = new Inscription();
        $coursModel = new Cours();

        $repartitionInscriptions = [];
        foreach ($inscriptionModel->repartitionParCoursPourFormateur($formateurId) as $ligne) {
            $repartitionInscriptions[] = ['label' => $ligne['titre'], 'value' => (int) $ligne['total']];
        }

        $this->view('formateur/statistiques', [
            'title' => 'Statistiques',
            'repartitionInscriptions' => $repartitionInscriptions,
            'coursParStatut' => [
                ['label' => 'Publies', 'value' => $coursModel->count(['formateur_id' => $formateurId, 'statut' => 'publie'])],
                ['label' => 'Brouillons', 'value' => $coursModel->count(['formateur_id' => $formateurId, 'statut' => 'brouillon'])],
            ],
        ], 'back');
    }
}
