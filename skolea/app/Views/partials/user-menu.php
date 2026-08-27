<?php
// Menu deroulant du compte connecte (photo/initiales, lien vers l'espace
// selon le role, deconnexion). Inclus dans le header, en desktop et en mobile.
?>
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
