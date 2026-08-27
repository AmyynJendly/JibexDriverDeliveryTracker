<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Utilisateur.php';
require_once __DIR__ . '/../../Controller/UtilisateurController.php';

if (!a_le_role('administrateur')) {
    flash_set('erreur', 'Acces reserve aux administrateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$utilisateurModel = new Utilisateur();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer') {
    $token = $_POST['_csrf'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(419);
        die('Session expiree, merci de recharger la page.');
    }

    $idASupprimer = (int) ($_POST['id'] ?? 0);
    if ($idASupprimer === (int) $_SESSION['utilisateur']['id']) {
        flash_set('erreur', 'Vous ne pouvez pas supprimer votre propre compte.');
    } else {
        (new UtilisateurController())->supprimer($idASupprimer);
        flash_set('succes', 'Utilisateur supprime avec succes.');
    }
    header('Location: admin_utilisateurs.php');
    exit;
}

$role = trim($_GET['role'] ?? '');
$recherche = trim($_GET['q'] ?? '');

$parPage = 8;
$page = max(1, (int) ($_GET['page'] ?? 1));
$total = $utilisateurModel->count($role !== '' ? $role : null, $recherche !== '' ? $recherche : null);
$totalPages = max(1, (int) ceil($total / $parPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $parPage;
$utilisateurs = $utilisateurModel->paginate($parPage, $offset, $role !== '' ? $role : null, $recherche !== '' ? $recherche : null);

$pageTitle = 'Utilisateurs';
require __DIR__ . '/includes/header.php';
?>

<div class="section-head">
    <div>
        <h2 style="margin:0;">Utilisateurs</h2>
        <p class="text-muted" style="margin:4px 0 0;"><?= (int) $total ?> compte(s) au total.</p>
    </div>
    <a href="admin_utilisateur_form.php" class="btn btn-primary">Ajouter un utilisateur</a>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="get" action="admin_utilisateurs.php" class="form-row" style="align-items:end;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="q">Recherche</label>
                <input type="text" id="q" name="q" class="form-control" placeholder="Nom, prenom ou email" value="<?= e($recherche) ?>">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="role">Role</label>
                <select id="role" name="role" class="form-control">
                    <option value="">Tous les roles</option>
                    <?php foreach (['administrateur', 'formateur', 'etudiant'] as $r): ?>
                        <option value="<?= e($r) ?>" <?= $role === $r ? 'selected' : '' ?>><?= e(role_label($r)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <button type="submit" class="btn btn-outline">Filtrer</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Inscrit le</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($utilisateurs === []): ?>
                    <tr><td colspan="5" class="text-soft">Aucun utilisateur ne correspond a ces criteres.</td></tr>
                <?php endif; ?>
                <?php foreach ($utilisateurs as $u): ?>
                    <tr>
                        <td><?= e($u['prenom'] . ' ' . $u['nom']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><span class="badge badge-primaire"><?= e(role_label($u['role'])) ?></span></td>
                        <td class="text-soft"><?= e(format_date($u['date_creation'])) ?></td>
                        <td class="cell-actions">
                            <a href="admin_utilisateur_form.php?id=<?= (int) $u['id'] ?>" class="btn btn-outline btn-sm">Modifier</a>
                            <form method="post" action="admin_utilisateurs.php" data-confirm="Supprimer cet utilisateur ?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../../pagination.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
