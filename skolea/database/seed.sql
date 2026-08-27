-- =========================================================================
-- Skolea - Donnees de demonstration
-- A executer apres schema.sql
-- Mot de passe commun pour tous les comptes de demo : Passer123!
-- =========================================================================

USE skolea;

-- -------------------------------------------------------------------------
-- Utilisateurs
-- -------------------------------------------------------------------------
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, statut, bio) VALUES
('Ben Romdhane', 'Sana',    'admin@skolea.tn',            '$2y$12$G0lP4BaaDOewq2udxNPWoefG2R9svFrlHiTl.gNDFKbCeoGIGsIpm', 'administrateur', 'actif', 'Responsable de la plateforme Skolea.'),
('Chaabane',     'Nabil',   'nabil.chaabane@skolea.tn',   '$2y$12$G0lP4BaaDOewq2udxNPWoefG2R9svFrlHiTl.gNDFKbCeoGIGsIpm', 'formateur',      'actif', 'Developpeur web et formateur PHP/JavaScript depuis 8 ans.'),
('Meddeb',       'Ines',    'ines.meddeb@skolea.tn',      '$2y$12$G0lP4BaaDOewq2udxNPWoefG2R9svFrlHiTl.gNDFKbCeoGIGsIpm', 'formateur',      'actif', 'Data scientist, passionnee par la vulgarisation du Machine Learning.'),
('Krichen',      'Yassine', 'yassine.krichen@skolea.tn',  '$2y$12$G0lP4BaaDOewq2udxNPWoefG2R9svFrlHiTl.gNDFKbCeoGIGsIpm', 'formateur',      'actif', 'Designer UI/UX et consultant en marketing digital.'),
('Ferjani',      'Rania',   'rania.ferjani@skolea.tn',    '$2y$12$G0lP4BaaDOewq2udxNPWoefG2R9svFrlHiTl.gNDFKbCeoGIGsIpm', 'etudiant',       'actif', NULL),
('Belhadj',      'Omar',    'omar.belhadj@skolea.tn',     '$2y$12$G0lP4BaaDOewq2udxNPWoefG2R9svFrlHiTl.gNDFKbCeoGIGsIpm', 'etudiant',       'actif', NULL),
('Trabelsi',     'Salma',   'salma.trabelsi@skolea.tn',   '$2y$12$G0lP4BaaDOewq2udxNPWoefG2R9svFrlHiTl.gNDFKbCeoGIGsIpm', 'etudiant',       'actif', NULL),
('Aouadi',       'Mehdi',   'mehdi.aouadi@skolea.tn',     '$2y$12$G0lP4BaaDOewq2udxNPWoefG2R9svFrlHiTl.gNDFKbCeoGIGsIpm', 'etudiant',       'actif', NULL),
('Chatti',       'Nour',    'nour.chatti@skolea.tn',      '$2y$12$G0lP4BaaDOewq2udxNPWoefG2R9svFrlHiTl.gNDFKbCeoGIGsIpm', 'etudiant',       'actif', NULL),
('Gharbi',       'Amine',   'amine.gharbi@skolea.tn',     '$2y$12$G0lP4BaaDOewq2udxNPWoefG2R9svFrlHiTl.gNDFKbCeoGIGsIpm', 'etudiant',       'suspendu', NULL);

-- -------------------------------------------------------------------------
-- Categories
-- -------------------------------------------------------------------------
INSERT INTO categories (nom, description) VALUES
('Developpement Web',   'Langages et frameworks pour construire des sites et applications web.'),
('Data Science',        'Analyse de donnees, statistiques et intelligence artificielle.'),
('Design UI/UX',        'Conception d''interfaces et experience utilisateur.'),
('Reseaux & Securite',  'Administration reseau et securite des systemes d''information.'),
('Marketing Digital',   'Strategies et outils du marketing en ligne.');

-- -------------------------------------------------------------------------
-- Cours
-- -------------------------------------------------------------------------
INSERT INTO cours (titre, description, categorie_id, formateur_id, niveau, statut) VALUES
('PHP & MySQL : les fondamentaux', 'Apprenez a construire des applications web dynamiques avec PHP 8 et MySQL, de la connexion a la base de donnees jusqu''aux requetes preparees.', 1, 2, 'debutant', 'publie'),
('JavaScript moderne (ES6+)', 'Maitrisez les fonctionnalites cles de JavaScript moderne : arrow functions, promesses, modules et programmation asynchrone.', 1, 2, 'intermediaire', 'publie'),
('Developper une API REST avec PHP', 'Concevez et securisez une API REST complete en PHP natif, sans framework, avec authentification par token.', 1, 2, 'avance', 'publie'),
('Introduction a Python pour la data', 'Decouvrez les bases du langage Python et ses structures de donnees pour demarrer en analyse de donnees.', 2, 3, 'debutant', 'publie'),
('Machine Learning : les bases', 'Comprenez les concepts fondamentaux du Machine Learning et entrainez votre premier modele.', 2, 3, 'intermediaire', 'publie'),
('Design d''interfaces avec Figma', 'Prenez en main Figma pour creer des maquettes et des systemes de design coherents.', 3, 4, 'debutant', 'publie'),
('Principes d''ergonomie web', 'Explorez les regles d''ergonomie et d''accessibilite pour concevoir des interfaces utilisables par tous.', 3, 4, 'intermediaire', 'brouillon'),
('Securiser une application web', 'Identifiez et corrigez les failles de securite les plus courantes (injection SQL, XSS, CSRF).', 4, 2, 'avance', 'publie'),
('Les fondamentaux du marketing digital', 'Construisez une strategie de contenu et vos premieres bases en referencement naturel (SEO).', 5, 4, 'debutant', 'publie');

-- -------------------------------------------------------------------------
-- Modules
-- -------------------------------------------------------------------------
INSERT INTO modules (cours_id, titre, description, ordre) VALUES
(1, 'Introduction a PHP 8',              'Syntaxe de base, variables, structures de controle.', 1),
(1, 'Connexion a MySQL avec PDO',        'Etablir une connexion securisee et executer des requetes preparees.', 2),
(1, 'Mini-projet : CRUD complet',        'Mettre en pratique via un mini-projet de gestion de contacts.', 3),
(2, 'Les nouveautes ES6+',               'let/const, arrow functions, template literals, destructuring.', 1),
(2, 'Programmation asynchrone',          'Callbacks, promesses et async/await.', 2),
(3, 'Concevoir une API RESTful',         'Ressources, verbes HTTP et codes de statut.', 1),
(3, 'Authentification par token',        'Mettre en place une authentification via jetons.', 2),
(4, 'Syntaxe et structures de donnees',  'Listes, tuples, dictionnaires en Python.', 1),
(4, 'Manipuler des donnees avec pandas', 'Charger et explorer un jeu de donnees.', 2),
(5, 'Qu''est-ce que le Machine Learning ?', 'Apprentissage supervise vs non supervise.', 1),
(5, 'Premier modele de regression',      'Entrainer et evaluer un modele simple.', 2),
(6, 'Prise en main de Figma',            'Interface, calques et composants.', 1),
(6, 'Creer un design system',            'Couleurs, typographies et composants reutilisables.', 2),
(7, 'Les 10 heuristiques de Nielsen',    'Principes fondamentaux d''utilisabilite.', 1),
(8, 'Failles courantes (XSS, injection SQL)', 'Comprendre les vecteurs d''attaque les plus frequents.', 1),
(8, 'Bonnes pratiques de securisation',  'Requetes preparees, echappement, CSRF, gestion des sessions.', 2),
(9, 'Strategie de contenu',              'Definir une ligne editoriale et un calendrier de publication.', 1),
(9, 'Introduction au SEO',               'Bases du referencement naturel.', 2);

-- -------------------------------------------------------------------------
-- Ressources
-- -------------------------------------------------------------------------
INSERT INTO ressources (module_id, titre, type, contenu, description) VALUES
(1,  'Support de cours - Introduction a PHP 8', 'document', 'support-introduction-php8.pdf', 'Slides du module.'),
(1,  'Video - Installer son environnement PHP', 'video', 'https://www.php.net/manual/fr/install.php', 'Guide officiel d''installation.'),
(2,  'Support de cours - PDO et requetes preparees', 'document', 'support-pdo-requetes-preparees.pdf', NULL),
(2,  'Quiz - Connexion a une base de donnees', 'quiz', 'quiz-connexion-pdo.json', '10 questions a choix multiple.'),
(3,  'Enonce du mini-projet CRUD', 'document', 'enonce-mini-projet-crud.pdf', NULL),
(4,  'Support de cours - ES6+', 'document', 'support-es6-plus.pdf', NULL),
(5,  'Video - Comprendre async/await', 'video', 'https://developer.mozilla.org/fr/docs/Web/JavaScript/Reference/Statements/async_function', NULL),
(6,  'Support de cours - API RESTful', 'document', 'support-api-restful.pdf', NULL),
(7,  'Quiz - Authentification et securite', 'quiz', 'quiz-authentification-token.json', NULL),
(8,  'Support de cours - Bases de Python', 'document', 'support-bases-python.pdf', NULL),
(9,  'Jeu de donnees d''exemple', 'document', 'dataset-exemple.csv', 'A utiliser pour les exercices pratiques.'),
(10, 'Support de cours - Introduction au ML', 'document', 'support-introduction-ml.pdf', NULL),
(11, 'Video - Entrainer un modele de regression', 'video', 'https://scikit-learn.org/stable/', NULL),
(12, 'Support de cours - Prise en main de Figma', 'document', 'support-prise-en-main-figma.pdf', NULL),
(13, 'Quiz - Design system', 'quiz', 'quiz-design-system.json', NULL),
(14, 'Support de cours - Heuristiques de Nielsen', 'document', 'support-heuristiques-nielsen.pdf', NULL),
(15, 'Support de cours - Failles web courantes', 'document', 'support-failles-web.pdf', NULL),
(16, 'Quiz - Bonnes pratiques de securisation', 'quiz', 'quiz-securisation.json', NULL),
(17, 'Support de cours - Strategie de contenu', 'document', 'support-strategie-contenu.pdf', NULL),
(18, 'Video - Les bases du SEO', 'video', 'https://developers.google.com/search/docs/fundamentals/seo-starter-guide', NULL);

-- -------------------------------------------------------------------------
-- Inscriptions
-- -------------------------------------------------------------------------
INSERT INTO inscriptions (etudiant_id, cours_id, statut, progression, modules_termines) VALUES
(5, 1, 'termine',  100, '1,2,3'),
(5, 4, 'en_cours',  40, NULL),
(6, 2, 'en_cours',  60, '4'),
(6, 6, 'en_cours',  20, NULL),
(7, 1, 'en_cours',  30, NULL),
(8, 5, 'en_cours',  50, '10'),
(8, 9, 'termine',  100, '17,18'),
(9, 6, 'en_cours',  75, '12'),
(10, 8, 'abandonne', 10, NULL);
