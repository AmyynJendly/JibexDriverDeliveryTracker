<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Cours.php';
require_once __DIR__ . '/../../Controller/CoursController.php';

if (!a_le_role('formateur')) {
    flash_set('erreur', 'Acces reserve aux formateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

// Petite demonstration de POO : creation d'un objet Cours via son
// constructeur, puis affichage avec var_dump() et avec la methode
// show() de la classe (attributs prives + accesseurs).
$coursDemo = new Cours('Introduction a PHP 8', 'Les bases du langage PHP moderne.', 1, 'debutant', 'publie');

$pageTitle = 'Demonstration POO';
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
    <a href="formateur_dashboard.php">Tableau de bord</a> / Demonstration POO
</div>

<div class="card" style="max-width:680px;">
    <div class="card-body">
        <h3 style="margin-top:0;">Objet cree via le constructeur</h3>
        <p class="text-muted">var_dump() de l'objet :</p>
        <pre style="background:var(--color-surface-alt);padding:14px;border-radius:var(--radius-sm);overflow:auto;font-size:.8rem;"><?php var_dump($coursDemo); ?></pre>

        <p class="text-muted">Affichage via la methode show() de la classe Cours :</p>
        <?php $coursDemo->show(); ?>
    </div>
</div>

<div class="card" style="max-width:680px;margin-top:20px;">
    <div class="card-body">
        <h3 style="margin-top:0;">Creer un objet a partir d'un formulaire</h3>
        <p class="text-muted">Ce formulaire envoie ses donnees en POST vers formateur_demo_verification.php.</p>
        <form method="post" action="formateur_demo_verification.php" novalidate>
            <div class="form-group">
                <label class="form-label" for="titre">Titre</label>
                <input type="text" id="titre" name="titre" class="form-control" placeholder="Ex : Les bases de SQL">
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <input type="text" id="description" name="description" class="form-control" placeholder="Courte description">
            </div>
            <div class="form-group">
                <label class="form-label" for="niveau">Niveau</label>
                <select id="niveau" name="niveau" class="form-control">
                    <option value="debutant">Debutant</option>
                    <option value="intermediaire">Intermediaire</option>
                    <option value="avance">Avance</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Creer l'objet</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
