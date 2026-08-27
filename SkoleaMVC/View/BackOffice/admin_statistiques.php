<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Cours.php';
require_once __DIR__ . '/../../Model/Inscription.php';
require_once __DIR__ . '/../../Model/Utilisateur.php';

if (!a_le_role('administrateur')) {
    flash_set('erreur', 'Acces reserve aux administrateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$coursModel = new Cours();
$inscriptionModel = new Inscription();
$utilisateurModel = new Utilisateur();

$coursParStatut = [];
foreach ($coursModel->countByStatut() as $ligne) {
    $label = $ligne['statut'] === 'publie' ? 'Publies' : 'Brouillons';
    $coursParStatut[] = ['label' => $label, 'value' => (int) $ligne['total']];
}

$inscriptionsParStatut = [];
foreach ($inscriptionModel->countByStatut() as $ligne) {
    if ($ligne['statut'] === 'en_cours') {
        $label = 'En cours';
    } elseif ($ligne['statut'] === 'termine') {
        $label = 'Termine';
    } else {
        $label = 'Abandonne';
    }
    $inscriptionsParStatut[] = ['label' => $label, 'value' => (int) $ligne['total']];
}

$inscriptionsParMois = [];
foreach ($utilisateurModel->inscriptionsParMois() as $ligne) {
    $inscriptionsParMois[] = ['label' => $ligne['mois'], 'value' => (int) $ligne['total']];
}

$repartitionCategorie = [];
foreach ($coursModel->repartitionParCategorie() as $ligne) {
    $repartitionCategorie[] = ['label' => $ligne['categorie'], 'value' => (int) $ligne['total']];
}

$pageTitle = 'Statistiques';
require __DIR__ . '/includes/header.php';
?>

<div class="grid grid-cols-2">
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Cours par statut</h3></div>
        <div class="card-body">
            <?php $items = $coursParStatut; require __DIR__ . '/../../bar_liste.php'; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Inscriptions par statut</h3></div>
        <div class="card-body">
            <?php $items = $inscriptionsParStatut; require __DIR__ . '/../../bar_liste.php'; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Cours publies par categorie</h3></div>
        <div class="card-body">
            <?php $items = $repartitionCategorie; require __DIR__ . '/../../bar_liste.php'; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Nouveaux comptes (6 derniers mois)</h3></div>
        <div class="card-body">
            <?php $items = $inscriptionsParMois; require __DIR__ . '/../../bar_liste.php'; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
