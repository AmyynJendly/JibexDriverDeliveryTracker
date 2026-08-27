<?php $user = current_user(); ?>
<header class="back-topbar">
    <button type="button" class="sidebar-toggle nav-toggle" aria-label="Ouvrir le menu"><span></span></button>
    <h1><?= e($title ?? 'Tableau de bord') ?></h1>
    <div class="spacer"></div>
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
                <a href="<?= e(url('/')) ?>">Voir le site public</a>
                <hr>
                <form method="post" action="<?= e(url('/deconnexion')) ?>">
                    <?= csrf_field() ?>
                    <button type="submit">Se deconnecter</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</header>
