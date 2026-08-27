<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validator;
use App\Models\Utilisateur;

// Page de profil, commune aux trois roles (le layout change selon le role).
final class ProfilController extends Controller
{
    public function show(): void
    {
        $this->requireAuth();

        $utilisateur = (new Utilisateur())->findById((int) Auth::id());

        $this->view('profil/show', [
            'title' => 'Mon profil',
            'utilisateur' => $utilisateur,
            'old' => $utilisateur,
            'errors' => [],
            'erreursMotDePasse' => [],
        ], $this->layoutSelonRole());
    }

    public function update(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $id = (int) Auth::id();
        $model = new Utilisateur();
        $utilisateur = $model->findById($id);

        $data = [
            'nom' => (string) $this->input('nom', ''),
            'prenom' => (string) $this->input('prenom', ''),
            'bio' => (string) $this->input('bio', ''),
        ];

        $validator = new Validator($data);
        $validator
            ->required('nom', 'Nom')->max('nom', 80, 'Nom')
            ->required('prenom', 'Prenom')->max('prenom', 80, 'Prenom')
            ->max('bio', 500, 'Bio');

        if ($validator->fails()) {
            $this->view('profil/show', [
                'title' => 'Mon profil',
                'utilisateur' => $utilisateur,
                'old' => $data,
                'errors' => $validator->errors(),
                'erreursMotDePasse' => [],
            ], $this->layoutSelonRole());

            return;
        }

        $model->updateProfil($id, $data);
        Auth::refresh($model->findById($id));

        flash_set('succes', 'Profil mis a jour avec succes.');
        $this->redirect('/profil');
    }

    public function updatePassword(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $id = (int) Auth::id();
        $model = new Utilisateur();
        $utilisateur = $model->findById($id);

        $data = [
            'mot_de_passe_actuel' => (string) $this->input('mot_de_passe_actuel', ''),
            'mot_de_passe' => (string) $this->input('mot_de_passe', ''),
            'mot_de_passe_confirmation' => (string) $this->input('mot_de_passe_confirmation', ''),
        ];

        $validator = new Validator($data);
        $validator
            ->required('mot_de_passe_actuel', 'Mot de passe actuel')
            ->required('mot_de_passe', 'Nouveau mot de passe')->min('mot_de_passe', 8, 'Nouveau mot de passe')
            ->matches('mot_de_passe_confirmation', 'mot_de_passe', 'Confirmation du mot de passe');

        if (!$validator->fails() && !password_verify($data['mot_de_passe_actuel'], $utilisateur['mot_de_passe'])) {
            $validator->addError('mot_de_passe_actuel', 'Mot de passe actuel incorrect.');
        }

        if ($validator->fails()) {
            $this->view('profil/show', [
                'title' => 'Mon profil',
                'utilisateur' => $utilisateur,
                'old' => $utilisateur,
                'errors' => [],
                'erreursMotDePasse' => $validator->errors(),
            ], $this->layoutSelonRole());

            return;
        }

        $model->updatePassword($id, password_hash($data['mot_de_passe'], PASSWORD_DEFAULT));
        flash_set('succes', 'Mot de passe modifie avec succes.');
        $this->redirect('/profil');
    }

    private function layoutSelonRole(): string
    {
        return in_array(Auth::role(), ['administrateur', 'formateur'], true) ? 'back' : 'front';
    }
}
