<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Paginator;
use App\Models\Categorie;
use App\Models\Cours;
use App\Models\Inscription;
use App\Models\Module;

// Catalogue public des cours. La consultation est libre, mais il faut
// etre connecte en tant qu'etudiant pour s'inscrire.
final class CoursController extends Controller
{
    private const PAR_PAGE = 6;

    public function index(): void
    {
        $categorieNom = (string) $this->input('categorie', '');
        $niveau = (string) $this->input('niveau', '');
        $recherche = (string) $this->input('q', '');

        $categorieModel = new Categorie();
        $categorie = $categorieNom !== '' ? $this->trouverCategorieParNom($categorieModel, $categorieNom) : null;

        $filtres = ['statut' => 'publie'];
        if ($categorie) {
            $filtres['categorie_id'] = $categorie['id'];
        }
        if ($niveau !== '') {
            $filtres['niveau'] = $niveau;
        }
        if ($recherche !== '') {
            $filtres['recherche'] = $recherche;
        }

        $coursModel = new Cours();
        $total = $coursModel->count($filtres);
        $paginator = Paginator::fromRequest($total, self::PAR_PAGE);
        $cours = $coursModel->paginate($paginator->parPage, $paginator->offset, $filtres);

        $this->view('site/cours-index', [
            'title' => 'Catalogue des cours',
            'cours' => $cours,
            'paginator' => $paginator,
            'categories' => $categorieModel->all(),
            'categorieNom' => $categorieNom,
            'niveau' => $niveau,
            'recherche' => $recherche,
        ]);
    }

    public function show(string $id): void
    {
        $coursModel = new Cours();
        $cours = $coursModel->find((int) $id);

        if (!$cours || $cours['statut'] !== 'publie') {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Cours introuvable']);

            return;
        }

        $inscription = null;
        if (Auth::check() && Auth::role() === 'etudiant') {
            $inscription = (new Inscription())->findByEtudiantEtCours((int) Auth::id(), $cours['id']);
        }

        $this->view('site/cours-show', [
            'title' => $cours['titre'],
            'cours' => $cours,
            'modules' => (new Module())->byCours($cours['id']),
            'inscription' => $inscription,
        ]);
    }

    public function inscrire(string $id): void
    {
        $this->requireRole('etudiant');
        $this->verifyCsrf();

        $coursModel = new Cours();
        $cours = $coursModel->find((int) $id);

        if (!$cours || $cours['statut'] !== 'publie') {
            flash_set('erreur', 'Ce cours est introuvable.');
            $this->redirect('/cours');
        }

        $inscriptionModel = new Inscription();
        if (!$inscriptionModel->findByEtudiantEtCours((int) Auth::id(), $cours['id'])) {
            $inscriptionModel->create((int) Auth::id(), $cours['id']);
            flash_set('succes', 'Inscription reussie ! Vous pouvez commencer le cours.');
        }

        $this->redirect('/mes-cours/' . $cours['id']);
    }

    private function trouverCategorieParNom(Categorie $model, string $nom): ?array
    {
        foreach ($model->all() as $categorie) {
            if ($categorie['nom'] === $nom) {
                return $categorie;
            }
        }

        return null;
    }
}
