<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\Categorie;
use PDOException;

final class CategorieController extends Controller
{
    public function index(): void
    {
        $this->requireRole('administrateur');

        $this->view('admin/categories/index', [
            'title' => 'Categories de cours',
            'categories' => (new Categorie())->allWithCoursCount(),
        ], 'back');
    }

    public function create(): void
    {
        $this->requireRole('administrateur');

        $this->view('admin/categories/form', [
            'title' => 'Ajouter une categorie',
            'categorie' => null,
            'old' => [],
            'errors' => [],
        ], 'back');
    }

    public function store(): void
    {
        $this->requireRole('administrateur');
        $this->verifyCsrf();

        [$data, $validator] = $this->validerFormulaire();

        if ($validator->fails()) {
            $this->view('admin/categories/form', [
                'title' => 'Ajouter une categorie',
                'categorie' => null,
                'old' => $data,
                'errors' => $validator->errors(),
            ], 'back');

            return;
        }

        (new Categorie())->create($data);
        flash_set('succes', 'Categorie creee avec succes.');
        $this->redirect('/admin/categories');
    }

    public function edit(string $id): void
    {
        $this->requireRole('administrateur');

        $categorie = (new Categorie())->find((int) $id);
        if (!$categorie) {
            flash_set('erreur', 'Categorie introuvable.');
            $this->redirect('/admin/categories');
        }

        $this->view('admin/categories/form', [
            'title' => 'Modifier une categorie',
            'categorie' => $categorie,
            'old' => $categorie,
            'errors' => [],
        ], 'back');
    }

    public function update(string $id): void
    {
        $this->requireRole('administrateur');
        $this->verifyCsrf();

        $idInt = (int) $id;
        $model = new Categorie();
        $categorie = $model->find($idInt);
        if (!$categorie) {
            flash_set('erreur', 'Categorie introuvable.');
            $this->redirect('/admin/categories');
        }

        [$data, $validator] = $this->validerFormulaire($idInt);

        if ($validator->fails()) {
            $this->view('admin/categories/form', [
                'title' => 'Modifier une categorie',
                'categorie' => $categorie,
                'old' => $data,
                'errors' => $validator->errors(),
            ], 'back');

            return;
        }

        $model->update($idInt, $data);
        flash_set('succes', 'Categorie mise a jour avec succes.');
        $this->redirect('/admin/categories');
    }

    public function destroy(string $id): void
    {
        $this->requireRole('administrateur');
        $this->verifyCsrf();

        $idInt = (int) $id;
        $model = new Categorie();

        if ($model->countCours($idInt) > 0) {
            flash_set('erreur', 'Impossible de supprimer : des cours sont encore rattaches a cette categorie.');
            $this->redirect('/admin/categories');
        }

        try {
            $model->delete($idInt);
            flash_set('succes', 'Categorie supprimee avec succes.');
        } catch (PDOException) {
            flash_set('erreur', 'Impossible de supprimer cette categorie.');
        }

        $this->redirect('/admin/categories');
    }

    // Retourne [donnees du formulaire, validateur rempli].
    private function validerFormulaire(?int $idActuel = null): array
    {
        $data = [
            'nom' => (string) $this->input('nom', ''),
            'description' => (string) $this->input('description', ''),
        ];

        $validator = new Validator($data);
        $validator->required('nom', 'Nom')->max('nom', 100, 'Nom')->max('description', 255, 'Description');

        if (!$validator->fails() && (new Categorie())->nomExists($data['nom'], $idActuel)) {
            $validator->addError('nom', 'Une categorie porte deja ce nom.');
        }

        return [$data, $validator];
    }
}
