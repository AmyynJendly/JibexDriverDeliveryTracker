<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Ressource.php';
require_once __DIR__ . '/../../Controller/RessourceController.php';

if (!a_le_role('formateur')) {
    flash_set('erreur', 'Acces reserve aux formateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$formateurId = (int) $_SESSION['utilisateur']['id'];
$ressourceController = new RessourceController();

// --- Suppression via un simple lien "Supprimer" (requete GET) ---
if (($_GET['action'] ?? '') === 'supprimer') {
    $aSupprimer = $ressourceController->trouverPourFormateur((int) ($_GET['id'] ?? 0), $formateurId);
    if (!$aSupprimer) {
        flash_set('erreur', "Cette ressource n'existe pas ou ne vous appartient pas.");
        header('Location: formateur_cours.php');
        exit;
    }

    $ressourceController->supprimer($aSupprimer['id']);
    flash_set('succes', 'Ressource supprimee avec succes.');
    header('Location: formateur_cours_show.php?id=' . $aSupprimer['cours_id']);
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$ressource = $ressourceController->trouverPourFormateur($id, $formateurId);

if (!$ressource) {
    flash_set('erreur', "Cette ressource n'existe pas ou ne vous appartient pas.");
    header('Location: formateur_cours.php');
    exit;
}

$old = $ressource;
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

    $validator = $ressourceController->modifier($id, $data, $_FILES['fichier'] ?? []);

    if ($validator->fails()) {
        $old = $data;
        $errors = $validator->errors();
    } else {
        flash_set('succes', 'Ressource mise a jour avec succes.');
        header('Location: formateur_cours_show.php?id=' . $ressource['cours_id']);
        exit;
    }
}

$pageTitle = 'Modifier la ressource';
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
    <a href="formateur_cours.php">Mes cours</a> /
    <a href="formateur_cours_show.php?id=<?= $ressource['cours_id'] ?>">Cours</a> /
    Modifier la ressource
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <p class="text-muted" style="margin-top:0;">Module : <strong><?= e($ressource['module_titre']) ?></strong></p>

        <form method="post" action="formateur_ressource_modifier.php?id=<?= $id ?>"
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
                <?php if (!empty($ressource['contenu'])): ?>
                    <p class="form-hint">Contenu actuel : <?= e($ressource['contenu']) ?> (laisser vide pour le conserver)</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description (optionnel)</label>
                <textarea id="description" name="description" class="form-control" rows="3"
                          placeholder="Un resume de ce que contient cette ressource"><?= old($old, 'description') ?></textarea>
            </div>

            <div class="cluster" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="formateur_cours_show.php?id=<?= $ressource['cours_id'] ?>" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
