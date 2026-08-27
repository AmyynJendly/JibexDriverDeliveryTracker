<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Cours.php';
require_once __DIR__ . '/../../Controller/CoursController.php';

if (!a_le_role('formateur')) {
    flash_set('erreur', 'Acces reserve aux formateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

// Suite de la demonstration POO : construit un objet Cours a partir
// des donnees du formulaire (via ses setters), puis l'affiche avec
// var_dump() et avec le controleur (methode afficherCours()).
$cours1 = new Cours();
$cours1->setTitre(trim($_POST['titre'] ?? ''));
$cours1->setDescription(trim($_POST['description'] ?? ''));
$cours1->setNiveau($_POST['niveau'] ?? 'debutant');

$controller = new CoursController();

$pageTitle = 'Verification';
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
    <a href="formateur_dashboard.php">Tableau de bord</a> /
    <a href="formateur_demo_poo.php">Demonstration POO</a> / Verification
</div>

<div class="card" style="max-width:680px;">
    <div class="card-body">
        <p class="text-muted" style="margin-top:0;">var_dump() de l'objet cree depuis le formulaire :</p>
        <pre style="background:var(--color-surface-alt);padding:14px;border-radius:var(--radius-sm);overflow:auto;font-size:.8rem;"><?php var_dump($cours1); ?></pre>

        <p class="text-muted">Affichage via le controleur (CoursController::afficherCours()) :</p>
        <?php $controller->afficherCours($cours1); ?>

        <a href="formateur_demo_poo.php" class="btn btn-ghost" style="margin-top:16px;">Retour</a>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
