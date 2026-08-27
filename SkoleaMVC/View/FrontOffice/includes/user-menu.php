<?php
// Menu du compte connecte, ou liens connexion/inscription si visiteur.
// Inclus deux fois dans header.php (version desktop et version mobile).
$utilisateur = utilisateur_connecte();
?>
<?php if ($utilisateur): ?>
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
            <a href="profil.php">Mon profil</a>
            <?php if ($utilisateur['role'] === 'administrateur'): ?>
                <a href="../BackOffice/admin_dashboard.php">Tableau de bord admin</a>
            <?php elseif ($utilisateur['role'] === 'formateur'): ?>
                <a href="../BackOffice/formateur_dashboard.php">Espace formateur</a>
            <?php else: ?>
                <a href="mes_cours.php">Mes cours</a>
            <?php endif; ?>
            <hr>
            <form method="post" action="deconnexion.php">
                <?= csrf_field() ?>
                <button type="submit">Se deconnecter</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <a href="connexion.php" class="btn btn-ghost btn-sm">Connexion</a>
    <a href="inscription.php" class="btn btn-primary btn-sm">Creer un compte</a>
<?php endif; ?>
