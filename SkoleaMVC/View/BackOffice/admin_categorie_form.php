<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Categorie.php';
require_once __DIR__ . '/../../Controller/CategorieController.php';

if (!a_le_role('administrateur')) {
    flash_set('erreur', 'Acces reserve aux administrateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$categorieModel = new Categorie();
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$estModification = $id !== null;
$categorie = null;

if ($estModification) {
    $categorie = $categorieModel->find($id);
    if (!$categorie) {
        flash_set('erreur', 'Categorie introuvable.');
        header('Location: admin_categories.php');
        exit;
    }
}

$old = $categorie ?: [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['_csrf'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(419);
        die('Session expiree, merci de recharger la page.');
    }

    $data = [
        'nom' => trim($_POST['nom'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
    ];

    $controller = new CategorieController();
    $validator = $estModification ? $controller->modifier($id, $data) : $controller->creer($data);

    if ($validator->fails()) {
        $old = $data;
        $errors = $validator->errors();
    } else {
        flash_set('succes', $estModification ? 'Categorie mise a jour avec succes.' : 'Categorie creee avec succes.');
        header('Location: admin_categories.php');
        exit;
    }
}

$pageTitle = $estModification ? 'Modifier une categorie' : 'Ajouter une categorie';
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
    <a href="admin_categories.php">Categories</a> / <?= $estModification ? 'Modifier' : 'Ajouter' ?>
</div>

<div class="card" style="max-width:560px;">
    <div class="card-body">
        <form method="post" action="admin_categorie_form.php<?= $estModification ? '?id=' . $id : '' ?>" novalidate data-validate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="nom">Nom de la categorie</label>
                <input type="text" id="nom" name="nom" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'nom') ?>" data-rule="required|max:100">
                <?php if (isset($errors['nom'])): ?><p class="form-error"><?= e($errors['nom']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description (optionnel)</label>
                <textarea id="description" name="description" class="form-control" rows="3"><?= old($old, 'description') ?></textarea>
            </div>

            <div class="cluster" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary"><?= $estModification ? 'Enregistrer' : 'Creer la categorie' ?></button>
                <a href="admin_categories.php" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
