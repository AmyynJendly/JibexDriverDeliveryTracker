-- =========================================================================
-- Skolea - Plateforme e-learning de gestion de cours
-- Schema de la base de donnees (MySQL / MariaDB)
-- =========================================================================

CREATE DATABASE IF NOT EXISTS skolea CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE skolea;

-- -------------------------------------------------------------------------
-- Utilisateurs (administrateur, formateur, etudiant)
-- -------------------------------------------------------------------------
CREATE TABLE utilisateurs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(80)  NOT NULL,
    prenom          VARCHAR(80)  NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe    VARCHAR(255) NOT NULL,
    role            ENUM('administrateur', 'formateur', 'etudiant') NOT NULL DEFAULT 'etudiant',
    statut          ENUM('actif', 'suspendu') NOT NULL DEFAULT 'actif',
    photo           VARCHAR(255) NULL,
    bio             VARCHAR(500) NULL,
    date_creation   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- -------------------------------------------------------------------------
-- Categories de cours (gerees par l'administrateur)
-- -------------------------------------------------------------------------
CREATE TABLE categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(100) NOT NULL UNIQUE,
    description     VARCHAR(255) NULL,
    date_creation   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- -------------------------------------------------------------------------
-- Cours (crees par un formateur, rattaches a une categorie)
-- -------------------------------------------------------------------------
CREATE TABLE cours (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre           VARCHAR(150) NOT NULL,
    description     TEXT NOT NULL,
    categorie_id    INT UNSIGNED NOT NULL,
    formateur_id    INT UNSIGNED NOT NULL,
    image           VARCHAR(255) NULL,
    niveau          ENUM('debutant', 'intermediaire', 'avance') NOT NULL DEFAULT 'debutant',
    statut          ENUM('brouillon', 'publie') NOT NULL DEFAULT 'brouillon',
    date_creation   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_maj        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cours_categorie FOREIGN KEY (categorie_id) REFERENCES categories(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_cours_formateur FOREIGN KEY (formateur_id) REFERENCES utilisateurs(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -------------------------------------------------------------------------
-- Modules (sequences pedagogiques d'un cours)
-- -------------------------------------------------------------------------
CREATE TABLE modules (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cours_id        INT UNSIGNED NOT NULL,
    titre           VARCHAR(150) NOT NULL,
    description     TEXT NULL,
    ordre           INT UNSIGNED NOT NULL DEFAULT 1,
    date_creation   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_module_cours FOREIGN KEY (cours_id) REFERENCES cours(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -------------------------------------------------------------------------
-- Ressources (documents, videos, quiz rattaches a un module)
-- -------------------------------------------------------------------------
CREATE TABLE ressources (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    module_id       INT UNSIGNED NOT NULL,
    titre           VARCHAR(150) NOT NULL,
    type            ENUM('document', 'video', 'quiz') NOT NULL,
    contenu         VARCHAR(255) NOT NULL COMMENT 'chemin du fichier ou URL',
    description     VARCHAR(255) NULL,
    date_creation   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ressource_module FOREIGN KEY (module_id) REFERENCES modules(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -------------------------------------------------------------------------
-- Inscriptions (un etudiant s'inscrit a un cours)
-- -------------------------------------------------------------------------
CREATE TABLE inscriptions (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    etudiant_id         INT UNSIGNED NOT NULL,
    cours_id            INT UNSIGNED NOT NULL,
    date_inscription    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut              ENUM('en_cours', 'termine', 'abandonne') NOT NULL DEFAULT 'en_cours',
    progression         TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'pourcentage 0-100',
    modules_termines    VARCHAR(500) NULL COMMENT 'liste d''ids de modules termines, separes par des virgules',
    CONSTRAINT fk_inscription_etudiant FOREIGN KEY (etudiant_id) REFERENCES utilisateurs(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_inscription_cours FOREIGN KEY (cours_id) REFERENCES cours(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT uq_inscription UNIQUE (etudiant_id, cours_id)
) ENGINE = InnoDB;

CREATE INDEX idx_cours_categorie ON cours(categorie_id);
CREATE INDEX idx_cours_formateur ON cours(formateur_id);
CREATE INDEX idx_modules_cours ON modules(cours_id);
CREATE INDEX idx_ressources_module ON ressources(module_id);
CREATE INDEX idx_inscriptions_cours ON inscriptions(cours_id);
