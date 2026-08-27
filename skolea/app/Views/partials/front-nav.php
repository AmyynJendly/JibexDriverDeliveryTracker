<?php
$user = current_user();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<header class="site-header">
    <div class="container">
        <?php include __DIR__ . '/brand.php'; ?>

        <nav class="main-nav">
            <a href="<?= e(url('/')) ?>" class="<?= $path === url('/') ? 'is-active' : '' ?>">Accueil</a>
            <a href="<?= e(url('/cours')) ?>" class="<?= $path === url('/cours') ? 'is-active' : '' ?>">Catalogue des cours</a>
            <?php if ($user && $user['role'] === 'etudiant'): ?>
                <a href="<?= e(url('/mes-cours')) ?>" class="<?= $path === url('/mes-cours') ? 'is-active' : '' ?>">Mes cours</a>
            <?php endif; ?>
            <a href="<?= e(url('/a-propos')) ?>" class="<?= $path === url('/a-propos') ? 'is-active' : '' ?>">A propos</a>

            <!-- memes liens de connexion, affiches uniquement dans le menu mobile -->
            <div class="nav-actions nav-actions-mobile">
                <?php if ($user): ?>
                    <?php include __DIR__ . '/user-menu.php'; ?>
                <?php else: ?>
                    <a href="<?= e(url('/connexion')) ?>" class="btn btn-ghost btn-sm">Connexion</a>
                    <a href="<?= e(url('/inscription')) ?>" class="btn btn-primary btn-sm">Creer un compte</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="nav-actions nav-actions-desktop">
            <?php if ($user): ?>
                <?php include __DIR__ . '/user-menu.php'; ?>
            <?php else: ?>
                <a href="<?= e(url('/connexion')) ?>" class="btn btn-ghost btn-sm">Connexion</a>
                <a href="<?= e(url('/inscription')) ?>" class="btn btn-primary btn-sm">Creer un compte</a>
            <?php endif; ?>
        </div>
        <button type="button" class="nav-toggle" aria-label="Ouvrir le menu"><span></span></button>
    </div>
</header>
