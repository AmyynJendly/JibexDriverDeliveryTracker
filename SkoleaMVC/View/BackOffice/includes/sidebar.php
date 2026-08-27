<?php
$utilisateur = utilisateur_connecte();
$pageActuelle = basename($_SERVER['PHP_SELF']);
?>
<aside class="back-sidebar">
    <a href="<?= $utilisateur['role'] === 'administrateur' ? 'admin_dashboard.php' : 'formateur_dashboard.php' ?>" class="brand">
        <svg class="brand-mark" width="30" height="30" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="64" height="64" rx="16" fill="#ffffff"/>
            <path d="M22 24c0-5 5-8 10-8s9 3 9 7c0 4-3 5.5-8 7-6 1.7-9 3.4-9 7.5 0 4.3 4 7.5 10 7.5 4.4 0 8-1.6 9.6-4.3" stroke="#DD9636" stroke-width="5" stroke-linecap="round" fill="none"/>
        </svg>
        <span>Skolea</span>
    </a>

    <nav class="back-nav">
        <?php if ($utilisateur['role'] === 'administrateur'): ?>
            <div class="nav-section">Administration</div>
            <a href="admin_dashboard.php" class="<?= $pageActuelle === 'admin_dashboard.php' ? 'is-active' : '' ?>">Tableau de bord</a>
            <a href="admin_utilisateurs.php" class="<?= in_array($pageActuelle, ['admin_utilisateurs.php', 'admin_utilisateur_form.php'], true) ? 'is-active' : '' ?>">Utilisateurs</a>
            <a href="admin_categories.php" class="<?= in_array($pageActuelle, ['admin_categories.php', 'admin_categorie_form.php'], true) ? 'is-active' : '' ?>">Categories de cours</a>
            <a href="admin_statistiques.php" class="<?= $pageActuelle === 'admin_statistiques.php' ? 'is-active' : '' ?>">Statistiques</a>
        <?php elseif ($utilisateur['role'] === 'formateur'): ?>
            <div class="nav-section">Espace formateur</div>
            <a href="formateur_dashboard.php" class="<?= $pageActuelle === 'formateur_dashboard.php' ? 'is-active' : '' ?>">Tableau de bord</a>
            <a href="formateur_cours.php" class="<?= in_array($pageActuelle, ['formateur_cours.php', 'formateur_cours_form.php', 'formateur_cours_show.php', 'formateur_module_form.php', 'formateur_ressource_form.php'], true) ? 'is-active' : '' ?>">Mes cours</a>
            <a href="formateur_statistiques.php" class="<?= $pageActuelle === 'formateur_statistiques.php' ? 'is-active' : '' ?>">Statistiques</a>
        <?php endif; ?>

        <div class="nav-section">Compte</div>
        <a href="../FrontOffice/profil.php">Mon profil</a>
        <a href="../FrontOffice/index.php">Voir le site public</a>
    </nav>
</aside>
