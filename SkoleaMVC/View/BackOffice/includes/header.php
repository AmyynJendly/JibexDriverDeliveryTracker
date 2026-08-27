<?php
// Inclus en haut de chaque page du BackOffice.
// Suppose que $pageTitle est defini et que l'utilisateur est deja
// verifie connecte + role autorise par la page elle-meme.
$utilisateur = utilisateur_connecte();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> - Skolea</title>
    <link rel="icon" type="image/svg+xml" href="../../assets/img/favicon.svg">
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
<div class="back-shell">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="back-main">
        <header class="back-topbar">
            <button type="button" class="sidebar-toggle nav-toggle" aria-label="Ouvrir le menu"><span></span></button>
            <h1><?= e($pageTitle) ?></h1>
            <div class="spacer"></div>
            <div class="user-menu">
                <button type="button" class="user-chip">
                    <span class="user-avatar"><?= e(mb_strtoupper(mb_substr($utilisateur['prenom'], 0, 1) . mb_substr($utilisateur['nom'], 0, 1))) ?></span>
                    <?= e($utilisateur['prenom']) ?>
                </button>
                <div class="user-dropdown">
                    <div style="padding:8px 12px 10px;">
                        <strong style="display:block;font-size:.86rem;"><?= e($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></strong>
                        <span class="text-soft" style="font-size:.78rem;"><?= e(role_label($utilisateur['role'])) ?></span>
                    </div>
                    <hr>
                    <a href="../FrontOffice/profil.php">Mon profil</a>
                    <a href="../FrontOffice/index.php">Voir le site public</a>
                    <hr>
                    <form method="post" action="../FrontOffice/deconnexion.php">
                        <?= csrf_field() ?>
                        <button type="submit">Se deconnecter</button>
                    </form>
                </div>
            </div>
        </header>
        <div class="back-content">
            <?php $flashes = flash_get(); ?>
            <?php foreach ($flashes as $type => $message): ?>
                <div class="alert alert-<?= e($type) ?>" data-flash><?= e($message) ?></div>
            <?php endforeach; ?>
