<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Utilisateur.php';
require_once __DIR__ . '/../../Model/Cours.php';
require_once __DIR__ . '/../../Model/Inscription.php';

if (!a_le_role('administrateur')) {
    flash_set('erreur', 'Acces reserve aux administrateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$utilisateurModel = new Utilisateur();
$coursModel = new Cours();
$inscriptionModel = new Inscription();

$totalInscriptions = 0;
foreach ($inscriptionModel->countByStatut() as $ligne) {
    $totalInscriptions += (int) $ligne['total'];
}

$repartitionCategorie = [];
foreach ($coursModel->repartitionParCategorie() as $ligne) {
    $repartitionCategorie[] = ['label' => $ligne['categorie'], 'value' => (int) $ligne['total']];
}

$repartitionRole = [];
foreach ($utilisateurModel->repartitionParRole() as $ligne) {
    $repartitionRole[] = ['label' => role_label($ligne['role']), 'value' => (int) $ligne['total']];
}

$derniersUtilisateurs = $utilisateurModel->paginate(5, 0);

$pageTitle = 'Tableau de bord';
require __DIR__ . '/includes/header.php';
?>

<div class="stat-grid">
    <div class="stat-card">
        <span class="stat-value"><?= (int) $utilisateurModel->count() ?></span>
        <span class="stat-label">Utilisateurs</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= (int) $utilisateurModel->countByRole('formateur') ?></span>
        <span class="stat-label">Formateurs</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= (int) $utilisateurModel->countByRole('etudiant') ?></span>
        <span class="stat-label">Etudiants</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= (int) $coursModel->count(['statut' => 'publie']) ?></span>
        <span class="stat-label">Cours publies</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= (int) $coursModel->count(['statut' => 'brouillon']) ?></span>
        <span class="stat-label">Cours en brouillon</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= (int) $totalInscriptions ?></span>
        <span class="stat-label">Inscriptions</span>
    </div>
</div>

<div class="grid grid-cols-2">
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Cours par categorie</h3></div>
        <div class="card-body">
            <?php $items = $repartitionCategorie; require __DIR__ . '/../../bar_liste.php'; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Utilisateurs par role</h3></div>
        <div class="card-body">
            <?php $items = $repartitionRole; require __DIR__ . '/../../bar_liste.php'; ?>
        </div>
    </div>
</div>

<div class="card" style="margin-top:24px;">
    <div class="card-header">
        <h3 style="margin:0;">Derniers utilisateurs inscrits</h3>
        <a href="admin_utilisateurs.php" class="btn btn-outline btn-sm">Voir tous</a>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Utilisateur</th><th>Email</th><th>Role</th><th>Inscrit le</th></tr></thead>
            <tbody>
                <?php foreach ($derniersUtilisateurs as $u): ?>
                    <tr>
                        <td><?= e($u['prenom'] . ' ' . $u['nom']) ?></td>
                        <td class="text-muted"><?= e($u['email']) ?></td>
                        <td><span class="badge badge-primaire"><?= e(role_label($u['role'])) ?></span></td>
                        <td class="text-soft"><?= e(format_date($u['date_creation'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
