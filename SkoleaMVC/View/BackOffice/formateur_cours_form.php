<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Cours.php';
require_once __DIR__ . '/../../Model/Categorie.php';
require_once __DIR__ . '/../../Controller/CoursController.php';

if (!a_le_role('formateur')) {
    flash_set('erreur', 'Acces reserve aux formateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$formateurId = (int) $_SESSION['utilisateur']['id'];
$coursModel = new Cours();
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$estModification = $id !== null;
$cours = null;

if ($estModification) {
    $cours = $coursModel->find($id);
    if (!$cours || (int) $cours['formateur_id'] !== $formateurId) {
        flash_set('erreur', "Ce cours n'existe pas ou ne vous appartient pas.");
        header('Location: formateur_cours.php');
        exit;
    }
}

$old = $cours ?: [];
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
        'categorie_id' => trim($_POST['categorie_id'] ?? ''),
        'niveau' => $_POST['niveau'] ?? 'debutant',
        'statut' => $_POST['statut'] ?? 'brouillon',
    ];
    $fichierImage = $_FILES['image'] ?? [];

    $controller = new CoursController();

    if ($estModification) {
        $validator = $controller->modifier($id, $data, $fichierImage);
        $nouvelId = $id;
    } else {
        list($validator, $nouvelId) = $controller->creer($data, $fichierImage, $formateurId);
    }

    if ($validator->fails()) {
        $old = $data;
        $errors = $validator->errors();
    } else {
        flash_set('succes', $estModification ? 'Cours mis a jour avec succes.' : 'Cours cree avec succes. Ajoutez maintenant des modules.');
        header('Location: formateur_cours_show.php?id=' . $nouvelId);
        exit;
    }
}

$categories = (new Categorie())->all();
$pageTitle = $estModification ? 'Modifier le cours' : 'Creer un cours';
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
    <a href="formateur_cours.php">Mes cours</a> / <?= $estModification ? 'Modifier' : 'Creer' ?>
</div>

<div class="card" style="max-width:680px;">
    <div class="card-body">
        <form method="post" action="formateur_cours_form.php<?= $estModification ? '?id=' . $id : '' ?>"
              enctype="multipart/form-data" novalidate data-validate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="titre">Titre du cours</label>
                <input type="text" id="titre" name="titre" class="form-control<?= isset($errors['titre']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'titre') ?>" data-rule="required|max:150">
                <?php if (isset($errors['titre'])): ?><p class="form-error"><?= e($errors['titre']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>"
                          rows="5" data-rule="required"><?= old($old, 'description') ?></textarea>
                <?php if (isset($errors['description'])): ?><p class="form-error"><?= e($errors['description']) ?></p><?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="categorie_id">Categorie</label>
                    <select id="categorie_id" name="categorie_id" class="form-control<?= isset($errors['categorie_id']) ? ' is-invalid' : '' ?>" data-rule="required">
                        <option value="">Choisir...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int) $cat['id'] ?>" <?= (string) ($old['categorie_id'] ?? '') === (string) $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['categorie_id'])): ?><p class="form-error"><?= e($errors['categorie_id']) ?></p><?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label" for="niveau">Niveau</label>
                    <select id="niveau" name="niveau" class="form-control">
                        <?php foreach (['debutant', 'intermediaire', 'avance'] as $n): ?>
                            <option value="<?= e($n) ?>" <?= ($old['niveau'] ?? 'debutant') === $n ? 'selected' : '' ?>><?= e(niveau_label($n)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="statut">Statut</label>
                    <select id="statut" name="statut" class="form-control">
                        <option value="brouillon" <?= ($old['statut'] ?? 'brouillon') === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                        <option value="publie" <?= ($old['statut'] ?? '') === 'publie' ? 'selected' : '' ?>>Publie</option>
                    </select>
                    <p class="form-hint">Un cours en brouillon n'apparait pas dans le catalogue public.</p>
                </div>
                <div class="form-group">
                    <label class="form-label" for="image">Image du cours (optionnel)</label>
                    <input type="file" id="image" name="image" class="form-control<?= isset($errors['image']) ? ' is-invalid' : '' ?>" accept=".jpg,.jpeg,.png,.webp">
                    <p class="form-hint">JPG, PNG ou WEBP, 3 Mo maximum.</p>
                    <?php if (isset($errors['image'])): ?><p class="form-error"><?= e($errors['image']) ?></p><?php endif; ?>
                </div>
            </div>

            <div class="cluster" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary"><?= $estModification ? 'Enregistrer' : 'Creer le cours' ?></button>
                <a href="formateur_cours.php" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
