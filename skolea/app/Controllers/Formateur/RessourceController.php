<?php

declare(strict_types=1);

namespace App\Controllers\Formateur;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Upload;
use App\Core\Validator;
use App\Models\Module;
use App\Models\Ressource;

final class RessourceController extends Controller
{
    private const TYPES = ['document', 'video', 'quiz'];

    public function create(string $moduleId): void
    {
        $this->requireRole('formateur');

        $module = $this->recupererModuleOuBloquer((int) $moduleId);

        $this->view('formateur/ressources/form', [
            'title' => 'Ajouter une ressource',
            'module' => $module,
            'ressource' => null,
            'old' => [],
            'errors' => [],
        ], 'back');
    }

    public function store(string $moduleId): void
    {
        $this->requireRole('formateur');
        $this->verifyCsrf();

        $module = $this->recupererModuleOuBloquer((int) $moduleId);
        [$data, $validator, $contenu] = $this->validerFormulaire();

        if ($validator->fails()) {
            $this->view('formateur/ressources/form', [
                'title' => 'Ajouter une ressource',
                'module' => $module,
                'ressource' => null,
                'old' => $data,
                'errors' => $validator->errors(),
            ], 'back');

            return;
        }

        (new Ressource())->create([
            'module_id' => $module['id'],
            'titre' => $data['titre'],
            'type' => $data['type'],
            'contenu' => $contenu,
            'description' => $data['description'],
        ]);

        flash_set('succes', 'Ressource ajoutee avec succes.');
        $this->redirect('/formateur/cours/' . $module['cours_id']);
    }

    public function edit(string $id): void
    {
        $this->requireRole('formateur');

        $ressource = $this->recupererRessourceOuBloquer((int) $id);

        $this->view('formateur/ressources/form', [
            'title' => 'Modifier la ressource',
            'module' => ['id' => $ressource['module_id'], 'titre' => $ressource['module_titre'], 'cours_id' => $ressource['cours_id']],
            'ressource' => $ressource,
            'old' => $ressource,
            'errors' => [],
        ], 'back');
    }

    public function update(string $id): void
    {
        $this->requireRole('formateur');
        $this->verifyCsrf();

        $ressource = $this->recupererRessourceOuBloquer((int) $id);
        [$data, $validator, $contenu] = $this->validerFormulaire(obligatoire: false);

        if ($validator->fails()) {
            $this->view('formateur/ressources/form', [
                'title' => 'Modifier la ressource',
                'module' => ['id' => $ressource['module_id'], 'titre' => $ressource['module_titre'], 'cours_id' => $ressource['cours_id']],
                'ressource' => $ressource,
                'old' => $data,
                'errors' => $validator->errors(),
            ], 'back');

            return;
        }

        (new Ressource())->update((int) $ressource['id'], [
            'titre' => $data['titre'],
            'type' => $data['type'],
            'contenu' => $contenu,
            'description' => $data['description'],
        ]);

        flash_set('succes', 'Ressource mise a jour avec succes.');
        $this->redirect('/formateur/cours/' . $ressource['cours_id']);
    }

    public function destroy(string $id): void
    {
        $this->requireRole('formateur');
        $this->verifyCsrf();

        $ressource = $this->recupererRessourceOuBloquer((int) $id);
        (new Ressource())->delete((int) $ressource['id']);

        flash_set('succes', 'Ressource supprimee avec succes.');
        $this->redirect('/formateur/cours/' . $ressource['cours_id']);
    }

    private function recupererModuleOuBloquer(int $moduleId): array
    {
        $module = (new Module())->findAvecCours($moduleId);

        if (!$module || (int) $module['formateur_id'] !== Auth::id()) {
            flash_set('erreur', "Ce module n'existe pas ou ne vous appartient pas.");
            $this->redirect('/formateur/cours');
        }

        return $module;
    }

    private function recupererRessourceOuBloquer(int $id): array
    {
        $ressource = (new Ressource())->findAvecCours($id);

        if (!$ressource || (int) $ressource['formateur_id'] !== Auth::id()) {
            flash_set('erreur', "Cette ressource n'existe pas ou ne vous appartient pas.");
            $this->redirect('/formateur/cours');
        }

        return $ressource;
    }

    /**
     * @return array{0: array<string, string>, 1: Validator, 2: ?string}
     */
    private function validerFormulaire(bool $obligatoire = true): array
    {
        $data = [
            'titre' => (string) $this->input('titre', ''),
            'type' => (string) $this->input('type', 'document'),
            'contenu' => (string) $this->input('contenu', ''),
            'description' => (string) $this->input('description', ''),
        ];

        $validator = new Validator($data);
        $validator
            ->required('titre', 'Titre')->max('titre', 150, 'Titre')
            ->required('type', 'Type')->in('type', self::TYPES, 'Type');

        $contenu = $data['contenu'] !== '' ? $data['contenu'] : null;

        if ($data['type'] === 'document') {
            try {
                $fichier = Upload::stocker($_FILES['fichier'] ?? [], 'ressources', ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'csv'], 8 * 1024 * 1024);
                if ($fichier !== null) {
                    $contenu = $fichier;
                }
            } catch (\RuntimeException $e) {
                $validator->addError('fichier', $e->getMessage());
            }
        }

        if ($obligatoire && $contenu === null) {
            $validator->addError('contenu', 'Fournissez un fichier ou une URL pour cette ressource.');
        }

        return [$data, $validator, $contenu];
    }
}
