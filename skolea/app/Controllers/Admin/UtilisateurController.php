<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Paginator;
use App\Core\Validator;
use App\Models\Utilisateur;

final class UtilisateurController extends Controller
{
    private const PAR_PAGE = 8;
    private const ROLES = ['administrateur', 'formateur', 'etudiant'];

    public function index(): void
    {
        $this->requireRole('administrateur');

        $model = new Utilisateur();
        $role = (string) $this->input('role', '');
        $recherche = (string) $this->input('q', '');

        $total = $model->count($role !== '' ? $role : null, $recherche !== '' ? $recherche : null);
        $paginator = Paginator::fromRequest($total, self::PAR_PAGE);
        $utilisateurs = $model->paginate($paginator->parPage, $paginator->offset, $role !== '' ? $role : null, $recherche !== '' ? $recherche : null);

        $this->view('admin/utilisateurs/index', [
            'title' => 'Utilisateurs',
            'utilisateurs' => $utilisateurs,
            'paginator' => $paginator,
            'role' => $role,
            'recherche' => $recherche,
        ], 'back');
    }

    public function create(): void
    {
        $this->requireRole('administrateur');

        $this->view('admin/utilisateurs/form', [
            'title' => 'Ajouter un utilisateur',
            'utilisateur' => null,
            'old' => [],
            'errors' => [],
        ], 'back');
    }

    public function store(): void
    {
        $this->requireRole('administrateur');
        $this->verifyCsrf();

        [$data, $errors] = $this->validerFormulaire(creation: true);

        if ($errors->fails()) {
            $this->view('admin/utilisateurs/form', [
                'title' => 'Ajouter un utilisateur',
                'utilisateur' => null,
                'old' => $data,
                'errors' => $errors->errors(),
            ], 'back');

            return;
        }

        $model = new Utilisateur();
        $model->create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'statut' => $data['statut'],
            'bio' => $data['bio'],
        ]);

        flash_set('succes', 'Utilisateur cree avec succes.');
        $this->redirect('/admin/utilisateurs');
    }

    public function edit(string $id): void
    {
        $this->requireRole('administrateur');

        $utilisateur = (new Utilisateur())->findById((int) $id);
        if (!$utilisateur) {
            $this->redirectAvecErreur();
        }

        $this->view('admin/utilisateurs/form', [
            'title' => 'Modifier un utilisateur',
            'utilisateur' => $utilisateur,
            'old' => $utilisateur,
            'errors' => [],
        ], 'back');
    }

    public function update(string $id): void
    {
        $this->requireRole('administrateur');
        $this->verifyCsrf();

        $idInt = (int) $id;
        $model = new Utilisateur();
        $utilisateur = $model->findById($idInt);
        if (!$utilisateur) {
            $this->redirectAvecErreur();
        }

        [$data, $errors] = $this->validerFormulaire(creation: false, idActuel: $idInt);

        if ($errors->fails()) {
            $this->view('admin/utilisateurs/form', [
                'title' => 'Modifier un utilisateur',
                'utilisateur' => $utilisateur,
                'old' => $data,
                'errors' => $errors->errors(),
            ], 'back');

            return;
        }

        if ($idInt === Auth::id() && $data['role'] !== 'administrateur') {
            flash_set('erreur', 'Vous ne pouvez pas retirer votre propre role d\'administrateur.');
            $this->redirect('/admin/utilisateurs/' . $idInt . '/modifier');
        }

        $model->update($idInt, $data);

        if ((string) $this->input('mot_de_passe', '') !== '') {
            $model->updatePassword($idInt, password_hash((string) $this->input('mot_de_passe'), PASSWORD_DEFAULT));
        }

        if ($idInt === Auth::id()) {
            Auth::refresh($model->findById($idInt));
        }

        flash_set('succes', 'Utilisateur mis a jour avec succes.');
        $this->redirect('/admin/utilisateurs');
    }

    public function destroy(string $id): void
    {
        $this->requireRole('administrateur');
        $this->verifyCsrf();

        $idInt = (int) $id;

        if ($idInt === Auth::id()) {
            flash_set('erreur', 'Vous ne pouvez pas supprimer votre propre compte.');
            $this->redirect('/admin/utilisateurs');
        }

        (new Utilisateur())->delete($idInt);
        flash_set('succes', 'Utilisateur supprime avec succes.');
        $this->redirect('/admin/utilisateurs');
    }

    /**
     * @return array{0: array<string, string>, 1: Validator}
     */
    private function validerFormulaire(bool $creation, ?int $idActuel = null): array
    {
        $data = [
            'nom' => (string) $this->input('nom', ''),
            'prenom' => (string) $this->input('prenom', ''),
            'email' => (string) $this->input('email', ''),
            'role' => (string) $this->input('role', 'etudiant'),
            'statut' => (string) $this->input('statut', 'actif'),
            'bio' => (string) $this->input('bio', ''),
            'mot_de_passe' => (string) $this->input('mot_de_passe', ''),
        ];

        $validator = new Validator($data);
        $validator
            ->required('nom', 'Nom')->max('nom', 80, 'Nom')
            ->required('prenom', 'Prenom')->max('prenom', 80, 'Prenom')
            ->required('email', 'Email')->email('email')
            ->required('role', 'Role')->in('role', self::ROLES, 'Role')
            ->required('statut', 'Statut')->in('statut', ['actif', 'suspendu'], 'Statut');

        if ($creation) {
            $validator->required('mot_de_passe', 'Mot de passe')->min('mot_de_passe', 8, 'Mot de passe');
        } elseif ($data['mot_de_passe'] !== '') {
            $validator->min('mot_de_passe', 8, 'Mot de passe');
        }

        if (!$validator->fails() && (new Utilisateur())->emailExists($data['email'], $idActuel)) {
            $validator->addError('email', 'Cette adresse email est deja utilisee.');
        }

        return [$data, $validator];
    }

    private function redirectAvecErreur(): never
    {
        flash_set('erreur', 'Utilisateur introuvable.');
        $this->redirect('/admin/utilisateurs');
    }
}
