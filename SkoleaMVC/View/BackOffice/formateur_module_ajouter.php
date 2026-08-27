<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Cours.php';
require_once __DIR__ . '/../../Model/Module.php';
require_once __DIR__ . '/../../Controller/ModuleController.php';

if (!a_le_role('formateur')) {
    flash_set('erreur', 'Acces reserve aux formateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$formateurId = (int) $_SESSION['utilisateur']['id'];
$coursId = (int) ($_GET['cours_id'] ?? 0);
$coursModel = new Cours();

if (!$coursModel->appartientAuFormateur($coursId, $formateurId)) {
    flash_set('erreur', "Ce cours n'existe pas ou ne vous appartient pas.");
    header('Location: formateur_cours.php');
    exit;
}

$cours = $coursModel->find($coursId);
$old = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['_csrf'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(419);
        die('Session expiree, merci de recharger la page.');
    }

    $data = [
        'titre' => trim($_POST['titre'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'ordre' => trim($_POST['ordre'] ?? ''),
    ];

    $validator = (new ModuleController())->creer($coursId, $data);

    if ($validator->fails()) {
        $old = $data;
        $errors = $validator->errors();
    } else {
        flash_set('succes', 'Module ajoute avec succes.');
        header('Location: formateur_cours_show.php?id=' . $coursId);
        exit;
    }
}

$pageTitle = 'Ajouter un module';
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
    <a href="formateur_cours.php">Mes cours</a> /
    <a href="formateur_cours_show.php?id=<?= $coursId ?>"><?= e($cours['titre']) ?></a> /
    Ajouter un module
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form method="post" action="formateur_module_ajouter.php?cours_id=<?= $coursId ?>" novalidate data-validate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="titre">Titre du module</label>
                <input type="text" id="titre" name="titre" class="form-control<?= isset($errors['titre']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'titre') ?>" placeholder="Ex : Introduction aux variables" data-rule="required|min:3|max:150">
                <?php if (isset($errors['titre'])): ?><p class="form-error"><?= e($errors['titre']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description (optionnel)</label>
                <textarea id="description" name="description" class="form-control" rows="4"
                          placeholder="Ce que l'etudiant va apprendre dans ce module"><?= old($old, 'description') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="ordre">Ordre d'affichage</label>
                <input type="number" id="ordre" name="ordre" min="1" class="form-control<?= isset($errors['ordre']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'ordre') ?>" placeholder="Laisser vide pour l'ajouter a la fin">
                <?php if (isset($errors['ordre'])): ?><p class="form-error"><?= e($errors['ordre']) ?></p><?php endif; ?>
            </div>

            <div class="cluster" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary">Ajouter le module</button>
                <a href="formateur_cours_show.php?id=<?= $coursId ?>" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
