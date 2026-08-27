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
- **MVC** ecrit a la main, sans routeur : chaque page du dossier `View/`
  est un fichier PHP accede directement par le navigateur, qui appelle
  les Controllers et Models
- **CSS** et **JavaScript vanilla** (systeme de design maison, aucun framework CSS/JS)
- Aucune dependance Composer / npm requise pour faire fonctionner le site

## Structure du projet

```
JibexDriverDeliveryTracker/
├── config.php                  # Connexion PDO (classe config::getConnexion())
├── database/
│   ├── schema.sql               # Creation des tables + contraintes
│   └── seed.sql                  # Donnees de demonstration
└── SkoleaMVC/
    ├── bootstrap.php             # session_start() + inclusion des fichiers communs
    ├── helpers.php                # Fonctions globales (e, old, flash, csrf, auth...)
    ├── Validator.php              # Validation cote serveur (fluide)
    ├── Upload.php                 # Televersement de fichiers (images, documents)
    ├── pagination.php             # Partiel : barre de pagination
    ├── bar_liste.php               # Partiel : barres de statistiques
    ├── Model/                     # Utilisateur, Categorie, Cours, Module,
    │                               # Ressource, Inscription (CRUD + jointures)
    ├── Controller/                 # Un controleur par entite, logique
    │                               # metier appelee directement par les vues
    ├── View/
    │   ├── FrontOffice/            # Site public + espace etudiant
    │   │   ├── includes/            # header.php, footer.php, user-menu.php
    │   │   └── *.php                 # index, connexion, inscription, cours,
    │   │                              # cours_detail, mes_cours, suivre_cours...
    │   └── BackOffice/              # Espaces administrateur et formateur
    │       ├── includes/            # header.php, footer.php, sidebar.php
    │       └── *.php                 # admin_*, formateur_*
    ├── assets/
    │   ├── css/app.css               # Systeme de design maison
    │   ├── js/validation.js          # Validation des formulaires (data-rule)
    │   ├── js/main.js                # Interactions UI (menu mobile, confirmations)
    │   └── img/favicon.svg
    └── uploads/                    # Images de cours et documents televerses
```

Il n'y a pas de point d'entree unique ni de routeur : chaque page de
`View/FrontOffice/` ou `View/BackOffice/` est accedee directement par son
URL (ex. `View/FrontOffice/cours.php?id=3`) et navigue vers les autres
pages via de simples liens relatifs `<a href="...">`.

## Installation

### Pre-requis

- PHP 8.0+ avec l'extension `pdo_mysql`
- MySQL ou MariaDB
- Un serveur web (Apache/XAMPP/WAMP/MAMP) **ou** simplement le serveur
  integre de PHP pour un test rapide en local

### 1. Base de donnees

```bash
mysql -u root -p -e "CREATE DATABASE skolea CHARACTER SET utf8mb4"
mysql -u root -p skolea < database/schema.sql
mysql -u root -p skolea < database/seed.sql   # optionnel : donnees de demo
```

### 2. Configuration

Les identifiants de connexion se trouvent directement dans `config.php`
(a la racine du projet) :

```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skolea";
```

Adaptez ces valeurs selon votre configuration MySQL locale.

### 3. Lancer le site

**Option A — serveur PHP integre (le plus rapide pour tester) :**

```bash
php -S localhost:8000
```

Puis ouvrez `http://localhost:8000/SkoleaMVC/View/FrontOffice/index.php`.

**Option B — Apache / XAMPP / WAMP :**

Pointez le *document root* de votre virtual host (ou le dossier
`htdocs`) vers la racine du projet (celle qui contient `config.php`),
puis ouvrez `SkoleaMVC/View/FrontOffice/index.php`.

> Le dossier `SkoleaMVC/uploads/` doit etre accessible en ecriture par
> le serveur web (pour les images de cours et les documents televerses
> par les formateurs).

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
  `novalidate`) **et** en PHP cote serveur (`Validator`), jamais
  uniquement via les attributs HTML natifs
- **Televersement de fichiers** controle (extension et taille
  limitees) pour les images de cours et les documents de ressources
- **Suivi de progression** : un etudiant coche les modules termines,
  la progression (%) et le statut de son inscription se recalculent
  automatiquement
- **Templates responsifs** distincts pour le front-office (site
  public + espace etudiant) et le back-office (administrateur /
  formateur), avec menu de navigation adapte a chaque role

## Choix techniques

- **Pas de routeur** : chaque page est un fichier PHP accede
  directement, dans l'esprit d'une architecture MVC simple sans
  dependance externe. La navigation se fait par liens relatifs entre
  les fichiers de `View/`.
- **Acces base de donnees** : une connexion PDO unique (singleton via
  `config::getConnexion()`), requetes preparees partout,
  `ATTR_EMULATE_PREPARES` desactive pour utiliser les vraies requetes
  preparees cote serveur MySQL.
- **Securite** : jeton CSRF verifie sur chaque action de modification,
  verification systematique de la propriete d'une ressource (un
  formateur ne peut modifier que ses propres cours/modules/ressources),
  controle d'acces par role sur chaque page sensible.
- **Design** : systeme de composants CSS ecrit a la main (variables de
  couleur, boutons, cartes, formulaires, tableaux, pagination...),
  sans framework CSS.
