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
        $nbInscriptionsTotal = array_sum(array_map(static fn ($c) => (int) $c['nb_inscrits'], $cours));

        $this->view('formateur/dashboard', [
            'title' => 'Tableau de bord',
            'nbCours' => $coursModel->count(['formateur_id' => $formateurId]),
            'nbCoursPublies' => $coursModel->count(['formateur_id' => $formateurId, 'statut' => 'publie']),
            'nbCoursBrouillon' => $coursModel->count(['formateur_id' => $formateurId, 'statut' => 'brouillon']),
            'nbInscriptionsTotal' => $nbInscriptionsTotal,
            'derniersCours' => $cours,
            'repartitionInscriptions' => array_map(
                static fn ($row) => ['label' => $row['titre'], 'value' => (int) $row['total']],
                $inscriptionModel->repartitionParCoursPourFormateur($formateurId)
            ),
        ], 'back');
    }

    public function statistiques(): void
    {
        $this->requireRole('formateur');

        $formateurId = (int) Auth::id();
        $inscriptionModel = new Inscription();
        $coursModel = new Cours();

        $this->view('formateur/statistiques', [
            'title' => 'Statistiques',
            'repartitionInscriptions' => array_map(
                static fn ($row) => ['label' => $row['titre'], 'value' => (int) $row['total']],
                $inscriptionModel->repartitionParCoursPourFormateur($formateurId)
            ),
            'coursParStatut' => [
                ['label' => 'Publies', 'value' => $coursModel->count(['formateur_id' => $formateurId, 'statut' => 'publie'])],
                ['label' => 'Brouillons', 'value' => $coursModel->count(['formateur_id' => $formateurId, 'statut' => 'brouillon'])],
            ],
        ], 'back');
    }
}
