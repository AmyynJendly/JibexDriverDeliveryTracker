<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Module.php';
require_once __DIR__ . '/../../Model/Ressource.php';
require_once __DIR__ . '/../../Controller/ModuleController.php';
require_once __DIR__ . '/../../Controller/RessourceController.php';

if (!a_le_role('formateur')) {
    flash_set('erreur', 'Acces reserve aux formateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$formateurId = (int) $_SESSION['utilisateur']['id'];
$moduleId = (int) ($_GET['module_id'] ?? 0);
$module = (new ModuleController())->trouverPourFormateur($moduleId, $formateurId);

if (!$module) {
    flash_set('erreur', "Ce module n'existe pas ou ne vous appartient pas.");
    header('Location: formateur_cours.php');
    exit;
}

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
        'type' => $_POST['type'] ?? 'document',
        'contenu' => trim($_POST['contenu'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
    ];

    $validator = (new RessourceController())->creer($moduleId, $data, $_FILES['fichier'] ?? []);

    if ($validator->fails()) {
        $old = $data;
        $errors = $validator->errors();
    } else {
        flash_set('succes', 'Ressource ajoutee avec succes.');
        header('Location: formateur_cours_show.php?id=' . $module['cours_id']);
        exit;
    }
}

$pageTitle = 'Ajouter une ressource';
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
    <a href="formateur_cours.php">Mes cours</a> /
    <a href="formateur_cours_show.php?id=<?= $module['cours_id'] ?>">Cours</a> /
    Ajouter une ressource
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <p class="text-muted" style="margin-top:0;">Module : <strong><?= e($module['titre']) ?></strong></p>

        <form method="post" action="formateur_ressource_ajouter.php?module_id=<?= $moduleId ?>"
              enctype="multipart/form-data" novalidate data-validate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="titre">Titre de la ressource</label>
                <input type="text" id="titre" name="titre" class="form-control<?= isset($errors['titre']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'titre') ?>" placeholder="Ex : Support de cours PDF" data-rule="required|min:3|max:150">
                <?php if (isset($errors['titre'])): ?><p class="form-error"><?= e($errors['titre']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="type">Type de ressource</label>
                <select id="type" name="type" class="form-control">
                    <?php foreach (['document', 'video', 'quiz'] as $t): ?>
                        <option value="<?= e($t) ?>" <?= ($old['type'] ?? 'document') === $t ? 'selected' : '' ?>><?= e(type_ressource_label($t)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="fichier">Fichier a televerser (pour un document)</label>
                <input type="file" id="fichier" name="fichier" class="form-control<?= isset($errors['fichier']) ? ' is-invalid' : '' ?>"
                       accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.csv">
                <p class="form-hint">PDF, Word, PowerPoint, ZIP ou CSV, 8 Mo maximum.</p>
                <?php if (isset($errors['fichier'])): ?><p class="form-error"><?= e($errors['fichier']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="contenu">Ou une URL (pour une video ou un quiz externe)</label>
                <input type="text" id="contenu" name="contenu" class="form-control<?= isset($errors['contenu']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'contenu') ?>" placeholder="https://...">
                <?php if (isset($errors['contenu'])): ?><p class="form-error"><?= e($errors['contenu']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description (optionnel)</label>
                <textarea id="description" name="description" class="form-control" rows="3"
                          placeholder="Un resume de ce que contient cette ressource"><?= old($old, 'description') ?></textarea>
            </div>

            <div class="cluster" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary">Ajouter la ressource</button>
                <a href="formateur_cours_show.php?id=<?= $module['cours_id'] ?>" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
