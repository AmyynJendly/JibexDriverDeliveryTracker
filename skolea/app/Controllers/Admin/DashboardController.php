<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Cours;
use App\Models\Inscription;
use App\Models\Utilisateur;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireRole('administrateur');

        $utilisateurModel = new Utilisateur();
        $coursModel = new Cours();
        $inscriptionModel = new Inscription();

        $totalInscriptions = 0;
        foreach ($inscriptionModel->countByStatut() as $ligne) {
            $totalInscriptions += (int) $ligne['total'];
        }

        $repartitionCategorie = [];
        foreach ($coursModel->repartitionParCategorie() as $ligne) {
            $repartitionCategorie[] = ['label' => $ligne['categorie'], 'value' => (int) $ligne['total']];
        }

        $repartitionRole = [];
        foreach ($utilisateurModel->repartitionParRole() as $ligne) {
            $repartitionRole[] = ['label' => role_label($ligne['role']), 'value' => (int) $ligne['total']];
        }

        $this->view('admin/dashboard', [
            'title' => 'Tableau de bord',
            'nbUtilisateurs' => $utilisateurModel->count(),
            'nbFormateurs' => $utilisateurModel->countByRole('formateur'),
            'nbEtudiants' => $utilisateurModel->countByRole('etudiant'),
            'nbCoursPublies' => $coursModel->count(['statut' => 'publie']),
            'nbCoursBrouillon' => $coursModel->count(['statut' => 'brouillon']),
            'nbInscriptions' => $totalInscriptions,
            'repartitionCategorie' => $repartitionCategorie,
            'repartitionRole' => $repartitionRole,
            'derniersUtilisateurs' => $utilisateurModel->paginate(5, 0),
        ], 'back');
    }

    public function statistiques(): void
    {
        $this->requireRole('administrateur');

        $coursModel = new Cours();
        $inscriptionModel = new Inscription();
        $utilisateurModel = new Utilisateur();

        $coursParStatut = [];
        foreach ($coursModel->countByStatut() as $ligne) {
            $label = $ligne['statut'] === 'publie' ? 'Publies' : 'Brouillons';
            $coursParStatut[] = ['label' => $label, 'value' => (int) $ligne['total']];
        }

        $inscriptionsParStatut = [];
        foreach ($inscriptionModel->countByStatut() as $ligne) {
            if ($ligne['statut'] === 'en_cours') {
                $label = 'En cours';
            } elseif ($ligne['statut'] === 'termine') {
                $label = 'Termine';
            } else {
                $label = 'Abandonne';
            }
            $inscriptionsParStatut[] = ['label' => $label, 'value' => (int) $ligne['total']];
        }

        $inscriptionsParMois = [];
        foreach ($utilisateurModel->inscriptionsParMois() as $ligne) {
            $inscriptionsParMois[] = ['label' => $ligne['mois'], 'value' => (int) $ligne['total']];
        }

        $repartitionCategorie = [];
        foreach ($coursModel->repartitionParCategorie() as $ligne) {
            $repartitionCategorie[] = ['label' => $ligne['categorie'], 'value' => (int) $ligne['total']];
        }

        $this->view('admin/statistiques', [
            'title' => 'Statistiques',
            'coursParStatut' => $coursParStatut,
            'inscriptionsParStatut' => $inscriptionsParStatut,
            'inscriptionsParMois' => $inscriptionsParMois,
            'repartitionCategorie' => $repartitionCategorie,
        ], 'back');
    }
}
