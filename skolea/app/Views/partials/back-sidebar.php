<?php
$user = current_user();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isActive = static fn (string $route) => $path === url($route) ? 'is-active' : '';
?>
<aside class="back-sidebar">
    <?php $variant = 'clair';
    include __DIR__ . '/brand.php'; ?>

    <nav class="back-nav">
        <?php if ($user && $user['role'] === 'administrateur'): ?>
            <div class="nav-section">Administration</div>
            <a href="<?= e(url('/admin')) ?>" class="<?= $isActive('/admin') ?>">Tableau de bord</a>
            <a href="<?= e(url('/admin/utilisateurs')) ?>" class="<?= $isActive('/admin/utilisateurs') ?>">Utilisateurs</a>
            <a href="<?= e(url('/admin/categories')) ?>" class="<?= $isActive('/admin/categories') ?>">Categories de cours</a>
            <a href="<?= e(url('/admin/statistiques')) ?>" class="<?= $isActive('/admin/statistiques') ?>">Statistiques</a>
        <?php elseif ($user && $user['role'] === 'formateur'): ?>
            <div class="nav-section">Espace formateur</div>
            <a href="<?= e(url('/formateur')) ?>" class="<?= $isActive('/formateur') ?>">Tableau de bord</a>
            <a href="<?= e(url('/formateur/cours')) ?>" class="<?= $isActive('/formateur/cours') ?>">Mes cours</a>
            <a href="<?= e(url('/formateur/statistiques')) ?>" class="<?= $isActive('/formateur/statistiques') ?>">Statistiques</a>
        <?php endif; ?>

        <div class="nav-section">Compte</div>
        <a href="<?= e(url('/profil')) ?>" class="<?= $isActive('/profil') ?>">Mon profil</a>
        <a href="<?= e(url('/')) ?>">Voir le site public</a>
    </nav>
</aside>
