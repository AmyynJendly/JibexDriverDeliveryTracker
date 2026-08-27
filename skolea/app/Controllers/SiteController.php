<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

/**
 * Pages publiques generales (accueil, a propos).
 */
final class SiteController extends Controller
{
    public function home(): void
    {
        $pdo = Database::getInstance();

        $nbCours = (int) $pdo->query("SELECT COUNT(*) FROM cours WHERE statut = 'publie'")->fetchColumn();
        $nbFormateurs = (int) $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'formateur'")->fetchColumn();
        $nbEtudiants = (int) $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'etudiant'")->fetchColumn();

        $stmt = $pdo->query("
            SELECT c.id, c.titre, c.description, c.niveau, cat.nom AS categorie_nom,
                   u.prenom AS formateur_prenom, u.nom AS formateur_nom
            FROM cours c
            INNER JOIN categories cat ON cat.id = c.categorie_id
            INNER JOIN utilisateurs u ON u.id = c.formateur_id
            WHERE c.statut = 'publie'
            ORDER BY c.date_creation DESC
            LIMIT 3
        ");
        $coursRecents = $stmt->fetchAll();

        $categories = $pdo->query('SELECT nom FROM categories ORDER BY nom')->fetchAll();

        $this->view('site/home', [
            'title' => 'Skolea',
            'nbCours' => $nbCours,
            'nbFormateurs' => $nbFormateurs,
            'nbEtudiants' => $nbEtudiants,
            'coursRecents' => $coursRecents,
            'categories' => $categories,
        ]);
    }

    public function about(): void
    {
        $this->view('site/about', ['title' => 'A propos']);
    }
}
