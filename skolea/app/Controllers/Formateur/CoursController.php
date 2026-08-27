<?php

declare(strict_types=1);

namespace App\Controllers\Formateur;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Paginator;
use App\Core\Upload;
use App\Core\Validator;
use App\Models\Categorie;
use App\Models\Cours;
use App\Models\Inscription;
use App\Models\Module;
use App\Models\Ressource;

final class CoursController extends Controller
{
    private const PAR_PAGE = 6;
    private const NIVEAUX = ['debutant', 'intermediaire', 'avance'];
    private const STATUTS = ['brouillon', 'publie'];

    public function index(): void
    {
        $this->requireRole('formateur');

        $formateurId = (int) Auth::id();
        $model = new Cours();
        $statut = (string) $this->input('statut', '');

        $filtres = ['formateur_id' => $formateurId];
        if ($statut !== '') {
            $filtres['statut'] = $statut;
        }

        $total = $model->count($filtres);
        $paginator = Paginator::fromRequest($total, self::PAR_PAGE);
        $cours = $model->paginate($paginator->parPage, $paginator->offset, $filtres);

        $this->view('formateur/cours/index', [
            'title' => 'Mes cours',
            'cours' => $cours,
            'paginator' => $paginator,
            'statut' => $statut,
        ], 'back');
    }

    public function create(): void
    {
        $this->requireRole('formateur');

        $this->view('formateur/cours/form', [
            'title' => 'Creer un cours',
            'cours' => null,
            'categories' => (new Categorie())->all(),
            'old' => [],
            'errors' => [],
        ], 'back');
    }

    public function store(): void
    {
        $this->requireRole('formateur');
        $this->verifyCsrf();

        [$data, $validator] = $this->validerFormulaire();
        $image = null;

        if (!$validator->fails()) {
            try {
                $image = Upload::stocker($_FILES['image'] ?? [], 'cours', ['jpg', 'jpeg', 'png', 'webp'], 3 * 1024 * 1024);
            } catch (\RuntimeException $e) {
                $validator->addError('image', $e->getMessage());
            }
        }

        if ($validator->fails()) {
            $this->view('formateur/cours/form', [
                'title' => 'Creer un cours',
                'cours' => null,
                'categories' => (new Categorie())->all(),
                'old' => $data,
                'errors' => $validator->errors(),
            ], 'back');

            return;
        }

        $model = new Cours();
        $id = $model->create([
            'titre' => $data['titre'],
            'description' => $data['description'],
            'categorie_id' => $data['categorie_id'],
            'formateur_id' => Auth::id(),
            'image' => $image,
            'niveau' => $data['niveau'],
            'statut' => $data['statut'],
        ]);

        flash_set('succes', 'Cours cree avec succes. Ajoutez maintenant des modules.');
        $this->redirect('/formateur/cours/' . $id);
    }

    public function show(string $id): void
    {
        $this->requireRole('formateur');

        $coursModel = new Cours();
        $cours = $this->recupererCoursOuBloquer($coursModel, (int) $id);
        $modules = (new Module())->byCours($cours['id']);

        $ressourceModel = new Ressource();
        $ressourcesParModule = [];
        foreach ($modules as $module) {
            $ressourcesParModule[$module['id']] = $ressourceModel->byModule((int) $module['id']);
        }

        $this->view('formateur/cours/show', [
            'title' => $cours['titre'],
            'cours' => $cours,
            'modules' => $modules,
            'ressourcesParModule' => $ressourcesParModule,
            'participants' => (new Inscription())->byCours($cours['id']),
        ], 'back');
    }

    public function edit(string $id): void
    {
        $this->requireRole('formateur');

        $coursModel = new Cours();
        $cours = $this->recupererCoursOuBloquer($coursModel, (int) $id);

        $this->view('formateur/cours/form', [
            'title' => 'Modifier le cours',
            'cours' => $cours,
            'categories' => (new Categorie())->all(),
            'old' => $cours,
            'errors' => [],
        ], 'back');
    }

    public function update(string $id): void
    {
        $this->requireRole('formateur');
        $this->verifyCsrf();

        $coursModel = new Cours();
        $cours = $this->recupererCoursOuBloquer($coursModel, (int) $id);

        [$data, $validator] = $this->validerFormulaire();
        $image = null;

        if (!$validator->fails()) {
            try {
                $image = Upload::stocker($_FILES['image'] ?? [], 'cours', ['jpg', 'jpeg', 'png', 'webp'], 3 * 1024 * 1024);
            } catch (\RuntimeException $e) {
                $validator->addError('image', $e->getMessage());
            }
        }

        if ($validator->fails()) {
            $this->view('formateur/cours/form', [
                'title' => 'Modifier le cours',
                'cours' => $cours,
                'categories' => (new Categorie())->all(),
                'old' => $data,
                'errors' => $validator->errors(),
            ], 'back');

            return;
        }

        $coursModel->update((int) $cours['id'], [
            'titre' => $data['titre'],
            'description' => $data['description'],
            'categorie_id' => $data['categorie_id'],
            'niveau' => $data['niveau'],
            'statut' => $data['statut'],
            'image' => $image,
        ]);

        flash_set('succes', 'Cours mis a jour avec succes.');
        $this->redirect('/formateur/cours/' . $cours['id']);
    }

    public function destroy(string $id): void
    {
        $this->requireRole('formateur');
        $this->verifyCsrf();

        $coursModel = new Cours();
        $cours = $this->recupererCoursOuBloquer($coursModel, (int) $id);

        $coursModel->delete((int) $cours['id']);
        flash_set('succes', 'Cours supprime avec succes.');
        $this->redirect('/formateur/cours');
    }

    private function recupererCoursOuBloquer(Cours $model, int $id): array
    {
        $cours = $model->find($id);

        if (!$cours || (int) $cours['formateur_id'] !== Auth::id()) {
            flash_set('erreur', "Ce cours n'existe pas ou ne vous appartient pas.");
            $this->redirect('/formateur/cours');
        }

        return $cours;
    }

    // Retourne [donnees du formulaire, validateur rempli].
    private function validerFormulaire(): array
    {
        $data = [
            'titre' => (string) $this->input('titre', ''),
            'description' => (string) $this->input('description', ''),
            'categorie_id' => (string) $this->input('categorie_id', ''),
            'niveau' => (string) $this->input('niveau', 'debutant'),
            'statut' => (string) $this->input('statut', 'brouillon'),
        ];

        $validator = new Validator($data);
        $validator
            ->required('titre', 'Titre')->max('titre', 150, 'Titre')
            ->required('description', 'Description')
            ->required('categorie_id', 'Categorie')->numeric('categorie_id', 'Categorie')
            ->required('niveau', 'Niveau')->in('niveau', self::NIVEAUX, 'Niveau')
            ->required('statut', 'Statut')->in('statut', self::STATUTS, 'Statut');

        if (!$validator->fails() && !(new Categorie())->find((int) $data['categorie_id'])) {
            $validator->addError('categorie_id', 'Categorie invalide.');
        }

        return [$data, $validator];
    }
}
