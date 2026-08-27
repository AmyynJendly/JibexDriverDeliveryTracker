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
$moduleController = new ModuleController();
$coursModel = new Cours();

// --- Suppression d'un module (formulaire poste depuis formateur_cours_show.php) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer') {
    $token = $_POST['_csrf'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(419);
        die('Session expiree, merci de recharger la page.');
    }

    $module = $moduleController->trouverPourFormateur((int) ($_POST['id'] ?? 0), $formateurId);
    if (!$module) {
        flash_set('erreur', "Ce module n'existe pas ou ne vous appartient pas.");
        header('Location: formateur_cours.php');
        exit;
    }

    $moduleController->supprimer($module['id']);
    flash_set('succes', 'Module supprime avec succes.');
    header('Location: formateur_cours_show.php?id=' . $module['cours_id']);
    exit;
}

// --- Creation ou modification ---
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$coursId = isset($_GET['cours_id']) ? (int) $_GET['cours_id'] : null;
$estModification = $id !== null;
$module = null;

if ($estModification) {
    $module = $moduleController->trouverPourFormateur($id, $formateurId);
    if (!$module) {
        flash_set('erreur', "Ce module n'existe pas ou ne vous appartient pas.");
        header('Location: formateur_cours.php');
        exit;
    }
    $coursId = (int) $module['cours_id'];
    $cours = ['id' => $coursId, 'titre' => $module['cours_titre']];
} else {
    if (!$coursModel->appartientAuFormateur($coursId, $formateurId)) {
        flash_set('erreur', "Ce cours n'existe pas ou ne vous appartient pas.");
        header('Location: formateur_cours.php');
        exit;
    }
    $cours = $coursModel->find($coursId);
}

$old = $module ?: [];
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

    $validator = $estModification
        ? $moduleController->modifier($id, $data)
        : $moduleController->creer($coursId, $data);

    if ($validator->fails()) {
        $old = $data;
        $errors = $validator->errors();
    } else {
        flash_set('succes', $estModification ? 'Module mis a jour avec succes.' : 'Module ajoute avec succes.');
        header('Location: formateur_cours_show.php?id=' . $coursId);
        exit;
    }
}

$pageTitle = $estModification ? 'Modifier le module' : 'Ajouter un module';
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
    <a href="formateur_cours.php">Mes cours</a> /
    <a href="formateur_cours_show.php?id=<?= $coursId ?>"><?= e($cours['titre']) ?></a> /
    <?= $estModification ? 'Modifier le module' : 'Ajouter un module' ?>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form method="post"
              action="formateur_module_form.php<?= $estModification ? '?id=' . $id : '?cours_id=' . $coursId ?>"
              novalidate data-validate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="titre">Titre du module</label>
                <input type="text" id="titre" name="titre" class="form-control<?= isset($errors['titre']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'titre') ?>" data-rule="required|max:150">
                <?php if (isset($errors['titre'])): ?><p class="form-error"><?= e($errors['titre']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description (optionnel)</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?= old($old, 'description') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="ordre">Ordre d'affichage</label>
                <input type="number" id="ordre" name="ordre" min="1" class="form-control<?= isset($errors['ordre']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'ordre') ?>" placeholder="Laisser vide pour l'ajouter a la fin">
                <?php if (isset($errors['ordre'])): ?><p class="form-error"><?= e($errors['ordre']) ?></p><?php endif; ?>
            </div>

            <div class="cluster" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary"><?= $estModification ? 'Enregistrer' : 'Ajouter le module' ?></button>
                <a href="formateur_cours_show.php?id=<?= $coursId ?>" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
