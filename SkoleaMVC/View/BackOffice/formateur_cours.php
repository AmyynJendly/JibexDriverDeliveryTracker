<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Cours.php';

if (!a_le_role('formateur')) {
    flash_set('erreur', 'Acces reserve aux formateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$formateurId = (int) $_SESSION['utilisateur']['id'];
$coursModel = new Cours();

$statut = trim($_GET['statut'] ?? '');
$filtres = ['formateur_id' => $formateurId];
if ($statut !== '') {
    $filtres['statut'] = $statut;
}

$parPage = 6;
$page = max(1, (int) ($_GET['page'] ?? 1));
$total = $coursModel->count($filtres);
$totalPages = max(1, (int) ceil($total / $parPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $parPage;
$cours = $coursModel->paginate($parPage, $offset, $filtres);

$pageTitle = 'Mes cours';
require __DIR__ . '/includes/header.php';
?>

<div class="section-head">
    <div>
        <h2 style="margin:0;">Mes cours</h2>
        <p class="text-muted" style="margin:4px 0 0;"><?= (int) $total ?> cours au total.</p>
    </div>
    <a href="formateur_cours_form.php" class="btn btn-primary">Creer un cours</a>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="get" action="formateur_cours.php" class="cluster">
            <label class="form-label" for="statut" style="margin:0;">Statut</label>
            <select id="statut" name="statut" class="form-control" style="max-width:220px;" onchange="this.form.submit()">
                <option value="">Tous</option>
                <option value="publie" <?= $statut === 'publie' ? 'selected' : '' ?>>Publies</option>
                <option value="brouillon" <?= $statut === 'brouillon' ? 'selected' : '' ?>>Brouillons</option>
            </select>
        </form>
    </div>
</div>

<?php if ($cours === []): ?>
    <div class="empty-state card">
        <h3>Aucun cours pour le moment</h3>
        <p>Creez votre premier cours pour commencer a ajouter des modules.</p>
        <a href="formateur_cours_form.php" class="btn btn-primary">Creer un cours</a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-3">
        <?php foreach ($cours as $c): ?>
            <article class="card course-card">
                <div class="course-thumb"><span><?= e($c['categorie_nom']) ?></span></div>
                <div class="card-body">
                    <h3><a href="formateur_cours_show.php?id=<?= (int) $c['id'] ?>"><?= e($c['titre']) ?></a></h3>
                    <div class="course-meta">
                        <?php if ($c['statut'] === 'publie'): ?>
                            <span class="badge badge-succes">Publie</span>
                        <?php else: ?>
                            <span class="badge badge-attente">Brouillon</span>
                        <?php endif; ?>
                        <span><?= (int) $c['nb_modules'] ?> module(s)</span>
                        <span><?= (int) $c['nb_inscrits'] ?> inscrit(s)</span>
                    </div>
                    <div class="card-footer">
                        <a href="formateur_cours_show.php?id=<?= (int) $c['id'] ?>" class="btn btn-primary btn-sm">Gerer</a>
                        <a href="formateur_cours_form.php?id=<?= (int) $c['id'] ?>" class="btn btn-outline btn-sm">Modifier</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php require __DIR__ . '/../../pagination.php'; ?>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
