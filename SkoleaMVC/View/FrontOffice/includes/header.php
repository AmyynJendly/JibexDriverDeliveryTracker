<?php
// Inclus en haut de chaque page du FrontOffice.
// $pageTitle (optionnel) peut etre defini avant l'include.
$utilisateur = utilisateur_connecte();
$pageActuelle = basename($_SERVER['PHP_SELF']);
$titre = isset($pageTitle) ? $pageTitle . ' - Skolea' : 'Skolea';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titre) ?></title>
    <link rel="icon" type="image/svg+xml" href="../../assets/img/favicon.svg">
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <a href="index.php" class="brand">
            <svg class="brand-mark" width="30" height="30" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="64" height="64" rx="16" fill="#2A2F6D"/>
                <path d="M22 24c0-5 5-8 10-8s9 3 9 7c0 4-3 5.5-8 7-6 1.7-9 3.4-9 7.5 0 4.3 4 7.5 10 7.5 4.4 0 8-1.6 9.6-4.3" stroke="#DD9636" stroke-width="5" stroke-linecap="round" fill="none"/>
            </svg>
            <span>Skolea</span>
        </a>

        <nav class="main-nav">
            <a href="index.php" class="<?= $pageActuelle === 'index.php' ? 'is-active' : '' ?>">Accueil</a>
            <a href="cours.php" class="<?= $pageActuelle === 'cours.php' || $pageActuelle === 'cours_detail.php' ? 'is-active' : '' ?>">Catalogue des cours</a>
            <?php if ($utilisateur && $utilisateur['role'] === 'etudiant'): ?>
                <a href="mes_cours.php" class="<?= $pageActuelle === 'mes_cours.php' || $pageActuelle === 'suivre_cours.php' ? 'is-active' : '' ?>">Mes cours</a>
            <?php endif; ?>
            <a href="a_propos.php" class="<?= $pageActuelle === 'a_propos.php' ? 'is-active' : '' ?>">A propos</a>
            <a href="a_propos.php#contact">Contact</a>

            <div class="nav-actions nav-actions-mobile">
                <?php include __DIR__ . '/user-menu.php'; ?>
            </div>
        </nav>

        <div class="nav-actions nav-actions-desktop">
            <?php include __DIR__ . '/user-menu.php'; ?>
        </div>
        <button type="button" class="nav-toggle" aria-label="Ouvrir le menu"><span></span></button>
    </div>
</header>

<main>
    <?php $flashes = flash_get(); ?>
    <?php if ($flashes !== []): ?>
        <div class="container" style="padding-top:20px;">
            <?php foreach ($flashes as $type => $message): ?>
                <div class="alert alert-<?= e($type) ?>" data-flash><?= e($message) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
