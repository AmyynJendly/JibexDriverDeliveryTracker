<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Categorie;
use App\Models\Cours;
use App\Models\Utilisateur;

/**
 * Pages publiques generales (accueil, a propos).
 */
final class SiteController extends Controller
{
    public function home(): void
    {
        $coursModel = new Cours();
        $utilisateurModel = new Utilisateur();

        $this->view('site/home', [
            'title' => 'Skolea',
            'nbCours' => $coursModel->count(['statut' => 'publie']),
            'nbFormateurs' => $utilisateurModel->countByRole('formateur'),
            'nbEtudiants' => $utilisateurModel->countByRole('etudiant'),
            'coursRecents' => $coursModel->recents(3),
            'categories' => (new Categorie())->all(),
        ]);
    }

    public function about(): void
    {
        $this->view('site/about', ['title' => 'A propos']);
    }
}
