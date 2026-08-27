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

$repartitionInscriptions = [];
foreach ($inscriptionModel->repartitionParCoursPourFormateur($formateurId) as $ligne) {
    $repartitionInscriptions[] = ['label' => $ligne['titre'], 'value' => (int) $ligne['total']];
}

$coursParStatut = [
    ['label' => 'Publies', 'value' => $coursModel->count(['formateur_id' => $formateurId, 'statut' => 'publie'])],
    ['label' => 'Brouillons', 'value' => $coursModel->count(['formateur_id' => $formateurId, 'statut' => 'brouillon'])],
];

$pageTitle = 'Statistiques';
require __DIR__ . '/includes/header.php';
?>

<div class="grid grid-cols-2">
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Inscriptions par cours</h3></div>
        <div class="card-body">
            <?php $items = $repartitionInscriptions; require __DIR__ . '/../../bar_liste.php'; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Cours par statut</h3></div>
        <div class="card-body">
            <?php $items = $coursParStatut; require __DIR__ . '/../../bar_liste.php'; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
