<?php
/** @var array|null $user */
$user = current_user();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isActive = static fn (string $route) => $path === url($route) ? 'is-active' : '';
?>
<header class="site-header">
    <div class="container">
        <?php include __DIR__ . '/brand.php'; ?>

        <nav class="main-nav">
            <a href="<?= e(url('/')) ?>" class="<?= $isActive('/') ?>">Accueil</a>
            <a href="<?= e(url('/cours')) ?>" class="<?= $isActive('/cours') ?>">Catalogue des cours</a>
            <?php if ($user && $user['role'] === 'etudiant'): ?>
                <a href="<?= e(url('/mes-cours')) ?>" class="<?= $isActive('/mes-cours') ?>">Mes cours</a>
            <?php endif; ?>
            <a href="<?= e(url('/a-propos')) ?>" class="<?= $isActive('/a-propos') ?>">A propos</a>
        </nav>

        <div class="nav-actions">
            <?php if ($user): ?>
                <div class="user-menu">
                    <button type="button" class="user-chip">
                        <span class="user-avatar"><?= e(mb_strtoupper(mb_substr($user['prenom'], 0, 1) . mb_substr($user['nom'], 0, 1))) ?></span>
                        <?= e($user['prenom']) ?>
                    </button>
                    <div class="user-dropdown">
                        <div style="padding:8px 12px 10px;">
                            <strong style="display:block;font-size:.86rem;"><?= e($user['prenom'] . ' ' . $user['nom']) ?></strong>
                            <span class="text-soft" style="font-size:.78rem;"><?= e(role_label($user['role'])) ?></span>
                        </div>
                        <hr>
                        <a href="<?= e(url('/profil')) ?>">Mon profil</a>
                        <?php if ($user['role'] === 'administrateur'): ?>
                            <a href="<?= e(url('/admin')) ?>">Tableau de bord admin</a>
                        <?php elseif ($user['role'] === 'formateur'): ?>
                            <a href="<?= e(url('/formateur')) ?>">Espace formateur</a>
                        <?php else: ?>
                            <a href="<?= e(url('/mes-cours')) ?>">Mes cours</a>
                        <?php endif; ?>
                        <hr>
                        <form method="post" action="<?= e(url('/deconnexion')) ?>">
                            <?= csrf_field() ?>
                            <button type="submit">Se deconnecter</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= e(url('/connexion')) ?>" class="btn btn-ghost btn-sm">Connexion</a>
                <a href="<?= e(url('/inscription')) ?>" class="btn btn-primary btn-sm">Creer un compte</a>
            <?php endif; ?>
            <button type="button" class="nav-toggle" aria-label="Ouvrir le menu"><span></span></button>
        </div>
    </div>
</header>
