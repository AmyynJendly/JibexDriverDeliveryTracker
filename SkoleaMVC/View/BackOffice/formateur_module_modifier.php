<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Module.php';
require_once __DIR__ . '/../../Controller/ModuleController.php';

if (!a_le_role('formateur')) {
    flash_set('erreur', 'Acces reserve aux formateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$formateurId = (int) $_SESSION['utilisateur']['id'];
$moduleController = new ModuleController();

// --- Suppression (formulaire poste depuis formateur_cours_show.php) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer') {
    $token = $_POST['_csrf'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(419);
        die('Session expiree, merci de recharger la page.');
    }

    $aSupprimer = $moduleController->trouverPourFormateur((int) ($_POST['id'] ?? 0), $formateurId);
    if (!$aSupprimer) {
        flash_set('erreur', "Ce module n'existe pas ou ne vous appartient pas.");
        header('Location: formateur_cours.php');
        exit;
    }

    $moduleController->supprimer($aSupprimer['id']);
    flash_set('succes', 'Module supprime avec succes.');
    header('Location: formateur_cours_show.php?id=' . $aSupprimer['cours_id']);
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$module = $moduleController->trouverPourFormateur($id, $formateurId);

if (!$module) {
    flash_set('erreur', "Ce module n'existe pas ou ne vous appartient pas.");
    header('Location: formateur_cours.php');
    exit;
}

$coursId = (int) $module['cours_id'];
$old = $module;
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

    $validator = $moduleController->modifier($id, $data);

    if ($validator->fails()) {
        $old = $data;
        $errors = $validator->errors();
    } else {
        flash_set('succes', 'Module mis a jour avec succes.');
        header('Location: formateur_cours_show.php?id=' . $coursId);
        exit;
    }
}

$pageTitle = 'Modifier le module';
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
    <a href="formateur_cours.php">Mes cours</a> /
    <a href="formateur_cours_show.php?id=<?= $coursId ?>"><?= e($module['cours_titre']) ?></a> /
    Modifier le module
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form method="post" action="formateur_module_modifier.php?id=<?= $id ?>" novalidate data-validate>
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
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="formateur_cours_show.php?id=<?= $coursId ?>" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
