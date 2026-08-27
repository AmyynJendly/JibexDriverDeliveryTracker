<?php

declare(strict_types=1);

namespace App\Controllers\Formateur;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validator;
use App\Models\Cours;
use App\Models\Module;

final class ModuleController extends Controller
{
    public function create(string $coursId): void
    {
        $this->requireRole('formateur');

        $cours = $this->recupererCoursOuBloquer((int) $coursId);

        $this->view('formateur/modules/form', [
            'title' => 'Ajouter un module',
            'cours' => $cours,
            'module' => null,
            'old' => [],
            'errors' => [],
        ], 'back');
    }

    public function store(string $coursId): void
    {
        $this->requireRole('formateur');
        $this->verifyCsrf();

        $cours = $this->recupererCoursOuBloquer((int) $coursId);
        $moduleModel = new Module();

        [$data, $validator] = $this->validerFormulaire();

        if ($validator->fails()) {
            $this->view('formateur/modules/form', [
                'title' => 'Ajouter un module',
                'cours' => $cours,
                'module' => null,
                'old' => $data,
                'errors' => $validator->errors(),
            ], 'back');

            return;
        }

        $moduleModel->create([
            'cours_id' => $cours['id'],
            'titre' => $data['titre'],
            'description' => $data['description'],
            'ordre' => $data['ordre'] !== '' ? (int) $data['ordre'] : $moduleModel->prochainOrdre((int) $cours['id']),
        ]);

        flash_set('succes', 'Module ajoute avec succes.');
        $this->redirect('/formateur/cours/' . $cours['id']);
    }

    public function edit(string $id): void
    {
        $this->requireRole('formateur');

        $module = $this->recupererModuleOuBloquer((int) $id);

        $this->view('formateur/modules/form', [
            'title' => 'Modifier le module',
            'cours' => ['id' => $module['cours_id'], 'titre' => $module['cours_titre']],
            'module' => $module,
            'old' => $module,
            'errors' => [],
        ], 'back');
    }

    public function update(string $id): void
    {
        $this->requireRole('formateur');
        $this->verifyCsrf();

        $module = $this->recupererModuleOuBloquer((int) $id);
        [$data, $validator] = $this->validerFormulaire();

        if ($validator->fails()) {
            $this->view('formateur/modules/form', [
                'title' => 'Modifier le module',
                'cours' => ['id' => $module['cours_id'], 'titre' => $module['cours_titre']],
                'module' => $module,
                'old' => $data,
                'errors' => $validator->errors(),
            ], 'back');

            return;
        }

        (new Module())->update((int) $module['id'], [
            'titre' => $data['titre'],
            'description' => $data['description'],
            'ordre' => $data['ordre'] !== '' ? (int) $data['ordre'] : (int) $module['ordre'],
        ]);

        flash_set('succes', 'Module mis a jour avec succes.');
        $this->redirect('/formateur/cours/' . $module['cours_id']);
    }

    public function destroy(string $id): void
    {
        $this->requireRole('formateur');
        $this->verifyCsrf();

        $module = $this->recupererModuleOuBloquer((int) $id);
        (new Module())->delete((int) $module['id']);

        flash_set('succes', 'Module supprime avec succes.');
        $this->redirect('/formateur/cours/' . $module['cours_id']);
    }

    private function recupererCoursOuBloquer(int $coursId): array
    {
        $cours = (new Cours())->find($coursId);

        if (!$cours || (int) $cours['formateur_id'] !== Auth::id()) {
            flash_set('erreur', "Ce cours n'existe pas ou ne vous appartient pas.");
            $this->redirect('/formateur/cours');
        }

        return $cours;
    }

    private function recupererModuleOuBloquer(int $id): array
    {
        $module = (new Module())->findAvecCours($id);

        if (!$module || (int) $module['formateur_id'] !== Auth::id()) {
            flash_set('erreur', "Ce module n'existe pas ou ne vous appartient pas.");
            $this->redirect('/formateur/cours');
        }

        return $module;
    }

    /**
     * @return array{0: array<string, string>, 1: Validator}
     */
    private function validerFormulaire(): array
    {
        $data = [
            'titre' => (string) $this->input('titre', ''),
            'description' => (string) $this->input('description', ''),
            'ordre' => (string) $this->input('ordre', ''),
        ];

        $validator = new Validator($data);
        $validator->required('titre', 'Titre')->max('titre', 150, 'Titre');

        if ($data['ordre'] !== '') {
            $validator->numeric('ordre', 'Ordre');
        }

        return [$data, $validator];
    }
}
