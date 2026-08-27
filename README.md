# Skolea

Plateforme e-learning de gestion de cours, developpee en **PHP 8** natif
(architecture **MVC** faite main, acces base de donnees exclusivement via
**PDO**, aucun framework PHP ou JS) dans le cadre du module *Projet
Technologies Web* (2A).

Trois espaces distincts partagent la meme base de code :

| Role | Peut faire |
|---|---|
| **Administrateur** | Gerer les comptes utilisateurs, creer/modifier/supprimer les categories de cours, consulter les statistiques globales de la plateforme |
| **Formateur** | Creer/modifier/supprimer ses propres cours, les organiser en modules, ajouter des ressources (documents, videos, quiz), suivre ses statistiques |
| **Etudiant** | Rechercher et filtrer les cours publies, s'inscrire, consulter les ressources, marquer les modules comme termines et suivre sa progression |

## Sommaire

- [Stack technique](#stack-technique)
- [Structure du projet](#structure-du-projet)
- [Installation](#installation)
- [Comptes de demonstration](#comptes-de-demonstration)
- [Fonctionnalites](#fonctionnalites)
- [Choix techniques](#choix-techniques)

## Stack technique

- **PHP 8** (aucun framework — Symfony/Laravel/Angular etc. sont exclus du sujet)
- **MySQL / MariaDB** via **PDO** uniquement (requetes preparees partout, `PDO::ATTR_EMULATE_PREPARES` desactive)
- **MVC** ecrit a la main : routeur, controleurs, modeles et vues sans dependance externe
- **CSS** et **JavaScript vanilla** (systeme de design maison, aucun framework CSS/JS)
- Aucune dependance Composer / npm requise pour faire fonctionner le site

## Structure du projet

```
skolea/
├── config/
│   └── config.php            # Connexion base de donnees, pagination
├── database/
│   ├── schema.sql             # Creation des tables + contraintes
│   └── seed.sql                # Donnees de demonstration
├── app/
│   ├── Core/                   # Router, Database (PDO), Controller, Auth,
│   │                           # Validator, Paginator, Upload, helpers.php
│   ├── Models/                 # Utilisateur, Categorie, Cours, Module,
│   │                           # Ressource, Inscription (CRUD + jointures)
│   ├── Controllers/
│   │   ├── Admin/              # Back-office administrateur
│   │   ├── Formateur/          # Espace formateur
│   │   ├── Etudiant/           # Espace etudiant
│   │   └── ...                 # Site public, authentification, profil
│   ├── Views/
│   │   ├── layouts/            # front.php (site public) / back.php (back-office)
│   │   ├── partials/           # nav, sidebar, pagination, messages flash...
│   │   └── admin/ formateur/ etudiant/ site/ auth/ profil/ errors/
│   └── routes.php              # Table de routage
└── public/                     # Racine web (document root du serveur)
    ├── index.php                # Point d'entree unique (front controller)
    ├── .htaccess                 # Reecriture d'URL vers index.php
    ├── assets/                   # css/app.css, js/main.js, js/validation.js
    └── uploads/                  # Images de cours et documents televerses
```

## Installation

### Pre-requis

- PHP 8.0+ avec l'extension `pdo_mysql`
- MySQL ou MariaDB
- Un serveur web (Apache/XAMPP/WAMP/MAMP) **ou** simplement le serveur
  integre de PHP pour un test rapide en local

### 1. Base de donnees

```bash
mysql -u root -p -e "CREATE DATABASE skolea CHARACTER SET utf8mb4"
mysql -u root -p skolea < skolea/database/schema.sql
mysql -u root -p skolea < skolea/database/seed.sql   # optionnel : donnees de demo
```

### 2. Configuration

Les identifiants de connexion se lisent depuis des variables
d'environnement (avec valeurs par defaut dans `skolea/config/config.php`) :

```php
'db' => [
    'host' => getenv('SKOLEA_DB_HOST') ?: '127.0.0.1',
    'name' => getenv('SKOLEA_DB_NAME') ?: 'skolea',
    'user' => getenv('SKOLEA_DB_USER') ?: 'skolea',
    'pass' => getenv('SKOLEA_DB_PASS') ?: 'skolea',
],
```

Adaptez ces valeurs (ou definissez les variables d'environnement
correspondantes) selon votre configuration MySQL locale.

### 3. Lancer le site

**Option A — serveur PHP integre (le plus rapide pour tester) :**

```bash
php -S localhost:8000 -t skolea/public
```

Puis ouvrez `http://localhost:8000`.

**Option B — Apache / XAMPP / WAMP :**

Pointez le *document root* de votre virtual host (ou le dossier
`htdocs`) vers `skolea/public/`. Le fichier `.htaccess` fourni gere la
reecriture des URL vers `index.php`. Assurez-vous que `mod_rewrite`
est active.

> Le dossier `skolea/public/uploads/` doit etre accessible en
> ecriture par le serveur web (pour les images de cours et les
> documents televerses par les formateurs).

## Comptes de demonstration

Si vous avez importe `seed.sql`, tous les comptes utilisent le mot de
passe **`Passer123!`** :

| Role | Email |
|---|---|
| Administrateur | `admin@skolea.tn` |
| Formateur | `nabil.chaabane@skolea.tn` |
| Etudiant | `rania.ferjani@skolea.tn` |

## Fonctionnalites

- **Authentification** : inscription (compte etudiant), connexion,
  deconnexion, mots de passe hashes (`password_hash`), protection CSRF
  sur tous les formulaires POST
- **CRUD complet** sur les 6 entites (Utilisateur, Categorie, Cours,
  Module, Ressource, Inscription), avec jointures SQL reelles
  (cours ↔ categories ↔ formateurs, inscriptions ↔ cours ↔ etudiants...)
- **Pagination** sur les listes de cours (catalogue, mes cours, cours
  d'un formateur) et d'utilisateurs (back-office admin)
- **Recherche et filtres** : catalogue par categorie/niveau/mot-cle,
  utilisateurs par role/recherche
- **Validation des saisies** en JavaScript (`assets/js/validation.js`,
  lit les regles `data-rule` de chaque champ, formulaires en
  `novalidate`) **et** en PHP cote serveur (`App\Core\Validator`),
  jamais uniquement via les attributs HTML natifs
- **Televersement de fichiers** controle (extension et taille
  limitees) pour les images de cours et les documents de ressources
- **Suivi de progression** : un etudiant coche les modules termines,
  la progression (%) et le statut de son inscription se recalculent
  automatiquement
- **Templates responsifs** distincts pour le front-office (site
  public + espace etudiant) et le back-office (administrateur /
  formateur), avec menu de navigation adapte a chaque role

## Choix techniques

- **Routeur maison** (`App\Core\Router`) : associe methode HTTP + chemin
  (avec segments dynamiques `{id}`) a une action de controleur.
- **Acces base de donnees** : une connexion PDO unique (singleton),
  requetes preparees partout, `ATTR_EMULATE_PREPARES` desactive pour
  utiliser les vraies requetes preparees cote serveur MySQL.
- **Securite** : jeton CSRF verifie sur chaque action de modification,
  verification systematique de la propriete d'une ressource (un
  formateur ne peut modifier que ses propres cours/modules/ressources),
  controle d'acces par role sur chaque route sensible.
- **Design** : systeme de composants CSS ecrit a la main (variables de
  couleur, boutons, cartes, formulaires, tableaux, pagination...),
  sans framework CSS.
