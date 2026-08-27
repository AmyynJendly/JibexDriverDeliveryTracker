<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validator;
use App\Models\Utilisateur;

// Inscription, connexion et deconnexion. L'inscription publique ne cree
// que des comptes etudiant : formateur et administrateur sont crees par
// un administrateur depuis le back-office.
final class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirectSelonRole();

            return;
        }

        $this->view('auth/login', ['title' => 'Connexion', 'old' => [], 'errors' => []]);
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $email = (string) $this->input('email', '');
        $motDePasse = (string) $this->input('mot_de_passe', '');

        $validator = new Validator(['email' => $email, 'mot_de_passe' => $motDePasse]);
        $validator->required('email', 'Email')->email('email')->required('mot_de_passe', 'Mot de passe');

        $utilisateurModel = new Utilisateur();
        $utilisateur = null;

        if (!$validator->fails()) {
            $utilisateur = $utilisateurModel->findByEmail($email);

            if (!$utilisateur || !password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
                $validator->addError('email', 'Email ou mot de passe incorrect.');
            }
        }

        if ($validator->fails()) {
            $this->view('auth/login', [
                'title' => 'Connexion',
                'old' => ['email' => $email],
                'errors' => $validator->errors(),
            ]);

            return;
        }

        Auth::login($utilisateur);
        flash_set('succes', 'Bienvenue, ' . $utilisateur['prenom'] . ' !');
        $this->redirectSelonRole();
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirectSelonRole();

            return;
        }

        $this->view('auth/register', ['title' => 'Creer un compte', 'old' => [], 'errors' => []]);
    }

    public function register(): void
    {
        $this->verifyCsrf();

        $data = [
            'nom' => (string) $this->input('nom', ''),
            'prenom' => (string) $this->input('prenom', ''),
            'email' => (string) $this->input('email', ''),
            'mot_de_passe' => (string) $this->input('mot_de_passe', ''),
            'mot_de_passe_confirmation' => (string) $this->input('mot_de_passe_confirmation', ''),
        ];

        $validator = new Validator($data);
        $validator
            ->required('nom', 'Nom')->max('nom', 80, 'Nom')
            ->required('prenom', 'Prenom')->max('prenom', 80, 'Prenom')
            ->required('email', 'Email')->email('email')->max('email', 150, 'Email')
            ->required('mot_de_passe', 'Mot de passe')->min('mot_de_passe', 8, 'Mot de passe')
            ->matches('mot_de_passe_confirmation', 'mot_de_passe', 'Confirmation du mot de passe');

        $utilisateurModel = new Utilisateur();

        if (!$validator->fails() && $utilisateurModel->emailExists($data['email'])) {
            $validator->addError('email', 'Un compte existe deja avec cette adresse email.');
        }

        if ($validator->fails()) {
            $this->view('auth/register', [
                'title' => 'Creer un compte',
                'old' => $data,
                'errors' => $validator->errors(),
            ]);

            return;
        }

        $id = $utilisateurModel->create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
            'role' => 'etudiant',
        ]);

        $utilisateur = $utilisateurModel->findById($id);
        Auth::login($utilisateur);
        flash_set('succes', 'Votre compte a ete cree avec succes. Bienvenue sur Skolea !');
        $this->redirect('/mes-cours');
    }

    public function logout(): void
    {
        $this->verifyCsrf();
        Auth::logout();
        flash_set('info', 'Vous avez ete deconnecte.');
        $this->redirect('/connexion');
    }

    private function redirectSelonRole(): void
    {
        $role = Auth::role();

        if ($role === 'administrateur') {
            $this->redirect('/admin');
        } elseif ($role === 'formateur') {
            $this->redirect('/formateur');
        } else {
            $this->redirect('/mes-cours');
        }
    }
}
