<?php
$user = current_user();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<aside class="back-sidebar">
    <?php $variant = 'clair';
    include __DIR__ . '/brand.php'; ?>

    <nav class="back-nav">
        <?php if ($user && $user['role'] === 'administrateur'): ?>
            <div class="nav-section">Administration</div>
            <a href="<?= e(url('/admin')) ?>" class="<?= $path === url('/admin') ? 'is-active' : '' ?>">Tableau de bord</a>
            <a href="<?= e(url('/admin/utilisateurs')) ?>" class="<?= $path === url('/admin/utilisateurs') ? 'is-active' : '' ?>">Utilisateurs</a>
            <a href="<?= e(url('/admin/categories')) ?>" class="<?= $path === url('/admin/categories') ? 'is-active' : '' ?>">Categories de cours</a>
            <a href="<?= e(url('/admin/statistiques')) ?>" class="<?= $path === url('/admin/statistiques') ? 'is-active' : '' ?>">Statistiques</a>
        <?php elseif ($user && $user['role'] === 'formateur'): ?>
            <div class="nav-section">Espace formateur</div>
            <a href="<?= e(url('/formateur')) ?>" class="<?= $path === url('/formateur') ? 'is-active' : '' ?>">Tableau de bord</a>
            <a href="<?= e(url('/formateur/cours')) ?>" class="<?= $path === url('/formateur/cours') ? 'is-active' : '' ?>">Mes cours</a>
            <a href="<?= e(url('/formateur/statistiques')) ?>" class="<?= $path === url('/formateur/statistiques') ? 'is-active' : '' ?>">Statistiques</a>
        <?php endif; ?>

        <div class="nav-section">Compte</div>
        <a href="<?= e(url('/profil')) ?>" class="<?= $path === url('/profil') ? 'is-active' : '' ?>">Mon profil</a>
        <a href="<?= e(url('/')) ?>">Voir le site public</a>
    </nav>
</aside>
