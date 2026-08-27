<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Categorie.php';
require_once __DIR__ . '/../../Controller/CategorieController.php';

if (!a_le_role('administrateur')) {
    flash_set('erreur', 'Acces reserve aux administrateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$controller = new CategorieController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer') {
    $token = $_POST['_csrf'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(419);
        die('Session expiree, merci de recharger la page.');
    }

    $erreur = $controller->supprimer((int) ($_POST['id'] ?? 0));
    if ($erreur) {
        flash_set('erreur', $erreur);
    } else {
        flash_set('succes', 'Categorie supprimee avec succes.');
    }
    header('Location: admin_categories.php');
    exit;
}

$categories = (new Categorie())->allWithCoursCount();

$pageTitle = 'Categories de cours';
require __DIR__ . '/includes/header.php';
?>

<div class="section-head">
    <div>
        <h2 style="margin:0;">Categories de cours</h2>
        <p class="text-muted" style="margin:4px 0 0;"><?= count($categories) ?> categorie(s).</p>
    </div>
    <a href="admin_categorie_ajouter.php" class="btn btn-primary">Ajouter une categorie</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Cours rattaches</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($categories === []): ?>
                    <tr><td colspan="4" class="text-soft">Aucune categorie pour le moment.</td></tr>
                <?php endif; ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><strong><?= e($cat['nom']) ?></strong></td>
                        <td class="text-muted"><?= e($cat['description'] ?? '') ?></td>
                        <td><span class="badge badge-neutre"><?= (int) $cat['nb_cours'] ?></span></td>
                        <td class="cell-actions">
                            <a href="admin_categorie_modifier.php?id=<?= (int) $cat['id'] ?>" class="btn btn-outline btn-sm">Modifier</a>
                            <form method="post" action="admin_categories.php" data-confirm="Supprimer cette categorie ?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id" value="<?= (int) $cat['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm" <?= $cat['nb_cours'] > 0 ? 'disabled title="Des cours utilisent encore cette categorie"' : '' ?>>Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
