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

        $this->view('admin/dashboard', [
            'title' => 'Tableau de bord',
            'nbUtilisateurs' => $utilisateurModel->count(),
            'nbFormateurs' => $utilisateurModel->countByRole('formateur'),
            'nbEtudiants' => $utilisateurModel->countByRole('etudiant'),
            'nbCoursPublies' => $coursModel->count(['statut' => 'publie']),
            'nbCoursBrouillon' => $coursModel->count(['statut' => 'brouillon']),
            'nbInscriptions' => array_sum(array_column($inscriptionModel->countByStatut(), 'total')),
            'repartitionCategorie' => array_map(
                static fn ($row) => ['label' => $row['categorie'], 'value' => (int) $row['total']],
                $coursModel->repartitionParCategorie()
            ),
            'repartitionRole' => array_map(
                static fn ($row) => ['label' => role_label($row['role']), 'value' => (int) $row['total']],
                $utilisateurModel->repartitionParRole()
            ),
            'derniersUtilisateurs' => $utilisateurModel->paginate(5, 0),
        ], 'back');
    }

    public function statistiques(): void
    {
        $this->requireRole('administrateur');

        $coursModel = new Cours();
        $inscriptionModel = new Inscription();
        $utilisateurModel = new Utilisateur();

        $this->view('admin/statistiques', [
            'title' => 'Statistiques',
            'coursParStatut' => array_map(
                static fn ($row) => ['label' => $row['statut'] === 'publie' ? 'Publies' : 'Brouillons', 'value' => (int) $row['total']],
                $coursModel->countByStatut()
            ),
            'inscriptionsParStatut' => array_map(
                static fn ($row) => ['label' => match ($row['statut']) {
                    'en_cours' => 'En cours',
                    'termine' => 'Termine',
                    default => 'Abandonne',
                }, 'value' => (int) $row['total']],
                $inscriptionModel->countByStatut()
            ),
            'inscriptionsParMois' => array_map(
                static fn ($row) => ['label' => $row['mois'], 'value' => (int) $row['total']],
                $utilisateurModel->inscriptionsParMois()
            ),
            'repartitionCategorie' => array_map(
                static fn ($row) => ['label' => $row['categorie'], 'value' => (int) $row['total']],
                $coursModel->repartitionParCategorie()
            ),
        ], 'back');
    }
}
