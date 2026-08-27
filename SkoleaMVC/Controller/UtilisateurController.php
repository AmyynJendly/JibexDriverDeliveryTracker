<?php
// Regroupe la logique metier liee aux utilisateurs : connexion, inscription
// publique, gestion par l'administrateur et modification du profil.
class UtilisateurController
{
    private $model;

    public function __construct()
    {
        $this->model = new Utilisateur();
    }

    // Retourne [utilisateur ou null, validateur]. Verifie le validateur
    // avec $validator->fails() avant d'utiliser l'utilisateur.
    public function connecter($email, $motDePasse)
    {
        $validator = new Validator(['email' => $email, 'mot_de_passe' => $motDePasse]);
        $validator->required('email', 'Email')->email('email')->required('mot_de_passe', 'Mot de passe');

        $utilisateur = null;

        if (!$validator->fails()) {
            $utilisateur = $this->model->findByEmail($email);

            if (!$utilisateur || !password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
                $validator->addError('email', 'Email ou mot de passe incorrect.');
                $utilisateur = null;
            }
        }

        return [$utilisateur, $validator];
    }

    // Inscription publique : cree toujours un compte etudiant.
    public function inscrire($data)
    {
        $validator = new Validator($data);
        $validator
            ->required('nom', 'Nom')->min('nom', 3, 'Nom')->max('nom', 80, 'Nom')->alpha('nom', 'Nom')
            ->required('prenom', 'Prenom')->min('prenom', 3, 'Prenom')->max('prenom', 80, 'Prenom')->alpha('prenom', 'Prenom')
            ->required('email', 'Email')->email('email')->max('email', 150, 'Email')
            ->required('mot_de_passe', 'Mot de passe')->min('mot_de_passe', 8, 'Mot de passe')
            ->matches('mot_de_passe_confirmation', 'mot_de_passe', 'Confirmation du mot de passe');

        if (!$validator->fails() && $this->model->emailExists($data['email'])) {
            $validator->addError('email', 'Un compte existe deja avec cette adresse email.');
        }

        if ($validator->fails()) {
            return [null, $validator];
        }

        $id = $this->model->create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
            'role' => 'etudiant',
        ]);

        return [$this->model->findById($id), $validator];
    }

    // --- Gestion des comptes par l'administrateur ---

    public function creerParAdmin($data)
    {
        $validator = $this->validerFormulaireAdmin($data, true, null);

        if ($validator->fails()) {
            return $validator;
        }

        $this->model->create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'bio' => $data['bio'],
        ]);

        return $validator;
    }

    public function modifierParAdmin($id, $data)
    {
        $validator = $this->validerFormulaireAdmin($data, false, $id);

        if ($validator->fails()) {
            return $validator;
        }

        $this->model->update($id, $data);

        if ($data['mot_de_passe'] !== '') {
            $this->model->updatePassword($id, password_hash($data['mot_de_passe'], PASSWORD_DEFAULT));
        }

        return $validator;
    }

    public function supprimer($id)
    {
        $this->model->delete($id);
    }

    private function validerFormulaireAdmin($data, $creation, $idActuel)
    {
        $validator = new Validator($data);
        $validator
            ->required('nom', 'Nom')->min('nom', 3, 'Nom')->max('nom', 80, 'Nom')->alpha('nom', 'Nom')
            ->required('prenom', 'Prenom')->min('prenom', 3, 'Prenom')->max('prenom', 80, 'Prenom')->alpha('prenom', 'Prenom')
            ->required('email', 'Email')->email('email')
            ->required('role', 'Role')->in('role', ['administrateur', 'formateur', 'etudiant'], 'Role');

        if ($creation) {
            $validator->required('mot_de_passe', 'Mot de passe')->min('mot_de_passe', 8, 'Mot de passe');
        } elseif ($data['mot_de_passe'] !== '') {
            $validator->min('mot_de_passe', 8, 'Mot de passe');
        }

        if (!$validator->fails() && $this->model->emailExists($data['email'], $idActuel)) {
            $validator->addError('email', 'Cette adresse email est deja utilisee.');
        }

        return $validator;
    }

    // --- Profil (les 3 roles) ---

    public function modifierProfil($id, $data)
    {
        $validator = new Validator($data);
        $validator
            ->required('nom', 'Nom')->min('nom', 3, 'Nom')->max('nom', 80, 'Nom')->alpha('nom', 'Nom')
            ->required('prenom', 'Prenom')->min('prenom', 3, 'Prenom')->max('prenom', 80, 'Prenom')->alpha('prenom', 'Prenom')
            ->max('bio', 500, 'Bio');

        if (!$validator->fails()) {
            $this->model->updateProfil($id, $data);
        }

        return $validator;
    }

    public function changerMotDePasse($id, $data)
    {
        $utilisateur = $this->model->findById($id);

        $validator = new Validator($data);
        $validator
            ->required('mot_de_passe_actuel', 'Mot de passe actuel')
            ->required('mot_de_passe', 'Nouveau mot de passe')->min('mot_de_passe', 8, 'Nouveau mot de passe')
            ->matches('mot_de_passe_confirmation', 'mot_de_passe', 'Confirmation du mot de passe');

        if (!$validator->fails() && !password_verify($data['mot_de_passe_actuel'], $utilisateur['mot_de_passe'])) {
            $validator->addError('mot_de_passe_actuel', 'Mot de passe actuel incorrect.');
        }

        if (!$validator->fails()) {
            $this->model->updatePassword($id, password_hash($data['mot_de_passe'], PASSWORD_DEFAULT));
        }

        return $validator;
    }
}
