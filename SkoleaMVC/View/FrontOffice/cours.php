<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Cours.php';
require_once __DIR__ . '/../../Model/Categorie.php';

$categorieNom = trim($_GET['categorie'] ?? '');
$niveau = trim($_GET['niveau'] ?? '');
$recherche = trim($_GET['q'] ?? '');

$categorieModel = new Categorie();
$categories = $categorieModel->all();

$filtres = ['statut' => 'publie'];
foreach ($categories as $cat) {
    if ($cat['nom'] === $categorieNom) {
        $filtres['categorie_id'] = $cat['id'];
    }
}
if ($niveau !== '') {
    $filtres['niveau'] = $niveau;
}
if ($recherche !== '') {
    $filtres['recherche'] = $recherche;
}

$coursModel = new Cours();
$parPage = 6;
$page = max(1, (int) ($_GET['page'] ?? 1));
$total = $coursModel->count($filtres);
$totalPages = max(1, (int) ceil($total / $parPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $parPage;
$cours = $coursModel->paginate($parPage, $offset, $filtres);

$pageTitle = 'Catalogue des cours';
require __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-bottom:24px;">
    <div class="container">
        <span class="hero-eyebrow">Catalogue</span>
        <h1>Explorez nos cours</h1>
        <p class="text-muted" style="max-width:56ch;">Filtrez par categorie, niveau ou mot-cle pour trouver le cours qui vous correspond.</p>

        <form method="get" action="cours.php" class="card" style="margin-top:24px;">
            <div class="card-body form-row" style="align-items:end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="q">Recherche</label>
                    <input type="text" id="q" name="q" class="form-control" placeholder="Titre ou mot-cle" value="<?= e($recherche) ?>">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="categorie">Categorie</label>
                    <select id="categorie" name="categorie" class="form-control">
                        <option value="">Toutes les categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= e($cat['nom']) ?>" <?= $categorieNom === $cat['nom'] ? 'selected' : '' ?>><?= e($cat['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="niveau">Niveau</label>
                    <select id="niveau" name="niveau" class="form-control">
                        <option value="">Tous les niveaux</option>
                        <?php foreach (['debutant', 'intermediaire', 'avance'] as $n): ?>
                            <option value="<?= e($n) ?>" <?= $niveau === $n ? 'selected' : '' ?>><?= e(niveau_label($n)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </div>
            </div>
        </form>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">
        <p class="text-soft" style="margin-bottom:18px;"><?= (int) $total ?> cours trouve(s).</p>

        <?php if ($cours === []): ?>
            <div class="empty-state card">
                <h3>Aucun cours ne correspond a votre recherche</h3>
                <p>Essayez d'autres criteres ou consultez le catalogue complet.</p>
                <a href="cours.php" class="btn btn-outline">Reinitialiser les filtres</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-3">
                <?php foreach ($cours as $c): ?>
                    <article class="card course-card">
                        <div class="course-thumb"><span><?= e($c['categorie_nom']) ?></span></div>
                        <div class="card-body">
                            <h3><a href="cours_detail.php?id=<?= (int) $c['id'] ?>"><?= e($c['titre']) ?></a></h3>
                            <div class="course-meta">
                                <span><?= e(niveau_label($c['niveau'])) ?></span>
                                <span>&middot;</span>
                                <span><?= e($c['formateur_prenom'] . ' ' . $c['formateur_nom']) ?></span>
                            </div>
                            <p class="course-desc"><?= e(mb_strimwidth($c['description'], 0, 110, '...')) ?></p>
                            <div class="card-footer">
                                <span class="text-soft" style="font-size:.8rem;"><?= (int) $c['nb_inscrits'] ?> inscrit(s)</span>
                                <a href="cours_detail.php?id=<?= (int) $c['id'] ?>" class="btn btn-primary btn-sm">Voir le cours</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php require __DIR__ . '/../../pagination.php'; ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
