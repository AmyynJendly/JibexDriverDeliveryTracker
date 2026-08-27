<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Cours.php';
require_once __DIR__ . '/../../Model/Inscription.php';

if (!a_le_role('formateur')) {
    flash_set('erreur', 'Acces reserve aux formateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$formateurId = (int) $_SESSION['utilisateur']['id'];
$coursModel = new Cours();
$inscriptionModel = new Inscription();

$derniersCours = $coursModel->paginate(5, 0, ['formateur_id' => $formateurId]);

$nbInscriptionsTotal = 0;
foreach ($derniersCours as $c) {
    $nbInscriptionsTotal += (int) $c['nb_inscrits'];
}

$repartitionInscriptions = [];
foreach ($inscriptionModel->repartitionParCoursPourFormateur($formateurId) as $ligne) {
    $repartitionInscriptions[] = ['label' => $ligne['titre'], 'value' => (int) $ligne['total']];
}

$pageTitle = 'Tableau de bord';
require __DIR__ . '/includes/header.php';
?>

<div class="stat-grid">
    <div class="stat-card">
        <span class="stat-value"><?= (int) $coursModel->count(['formateur_id' => $formateurId]) ?></span>
        <span class="stat-label">Cours au total</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= (int) $coursModel->count(['formateur_id' => $formateurId, 'statut' => 'publie']) ?></span>
        <span class="stat-label">Cours publies</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= (int) $coursModel->count(['formateur_id' => $formateurId, 'statut' => 'brouillon']) ?></span>
        <span class="stat-label">Brouillons</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= (int) $nbInscriptionsTotal ?></span>
        <span class="stat-label">Etudiants inscrits</span>
    </div>
</div>

<div class="grid grid-cols-2">
    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;">Mes cours recents</h3>
            <a href="formateur_cours.php" class="btn btn-outline btn-sm">Voir tous</a>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Titre</th><th>Statut</th><th>Inscrits</th></tr></thead>
                <tbody>
                    <?php if ($derniersCours === []): ?>
                        <tr><td colspan="3" class="text-soft">Aucun cours pour le moment.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($derniersCours as $c): ?>
                        <tr>
                            <td><a href="formateur_cours_show.php?id=<?= (int) $c['id'] ?>"><?= e($c['titre']) ?></a></td>
                            <td>
                                <?php if ($c['statut'] === 'publie'): ?>
                                    <span class="badge badge-succes">Publie</span>
                                <?php else: ?>
                                    <span class="badge badge-attente">Brouillon</span>
                                <?php endif; ?>
                            </td>
                            <td><?= (int) $c['nb_inscrits'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Inscriptions par cours</h3></div>
        <div class="card-body">
            <?php $items = $repartitionInscriptions; require __DIR__ . '/../../bar_liste.php'; ?>
        </div>
    </div>
</div>

<div class="card" style="margin-top:24px;">
    <div class="card-body cluster" style="justify-content:space-between;">
        <div>
            <h3 style="margin:0 0 4px;">Pret a creer un nouveau cours ?</h3>
            <p class="text-muted" style="margin:0;">Ajoutez un titre, une categorie et commencez a structurer vos modules.</p>
        </div>
        <a href="formateur_cours_ajouter.php" class="btn btn-primary">Creer un cours</a>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
