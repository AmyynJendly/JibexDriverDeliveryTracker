<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Cours.php';
require_once __DIR__ . '/../../Model/Categorie.php';
require_once __DIR__ . '/../../Model/Utilisateur.php';

$coursModel = new Cours();
$nbCours = $coursModel->count(['statut' => 'publie']);
$coursRecents = $coursModel->recents(3);

$utilisateurModel = new Utilisateur();
$nbFormateurs = $utilisateurModel->countByRole('formateur');
$nbEtudiants = $utilisateurModel->countByRole('etudiant');

$categories = (new Categorie())->all();

$pageTitle = 'Skolea';
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div>
            <span class="hero-eyebrow">Plateforme e-learning</span>
            <h1>Apprenez a votre rythme, avec des formateurs qui suivent vos progres.</h1>
            <p class="hero-lead">
                Skolea reunit administrateurs, formateurs et etudiants autour d'un meme
                espace : creation de cours, suivi des inscriptions et parcours pedagogiques
                organises en modules et ressources.
            </p>
            <div class="cluster">
                <a href="cours.php" class="btn btn-primary">Explorer le catalogue</a>
                <a href="inscription.php" class="btn btn-outline">Creer un compte gratuit</a>
            </div>
            <div class="hero-stats">
                <div>
                    <strong><?= (int) $nbCours ?>+</strong>
                    <span>cours publies</span>
                </div>
                <div>
                    <strong><?= (int) $nbFormateurs ?></strong>
                    <span>formateurs actifs</span>
                </div>
                <div>
                    <strong><?= (int) $nbEtudiants ?></strong>
                    <span>etudiants inscrits</span>
                </div>
            </div>
        </div>
        <div class="hero-art">
            <div class="hero-art-card">
                <p class="text-muted" style="margin:0 0 8px;font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">Progression du module</p>
                <div class="progress-bar"><span style="width:72%"></span></div>
                <p class="text-soft" style="margin:8px 0 0;font-size:.8rem;">3 modules sur 4 termines</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Des parcours pour tous les niveaux</h2>
                <p class="text-muted">Parcourez les categories proposees par nos formateurs.</p>
            </div>
        </div>
        <div class="tag-list">
            <?php foreach ($categories as $categorie): ?>
                <a href="cours.php?categorie=<?= urlencode($categorie['nom']) ?>" class="badge badge-primaire" style="padding:8px 16px;font-size:.82rem;text-transform:none;letter-spacing:0;">
                    <?= e($categorie['nom']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" style="background:var(--color-surface);border-top:1px solid var(--color-border);border-bottom:1px solid var(--color-border);">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Cours recemment publies</h2>
                <p class="text-muted">Un apercu des derniers contenus ajoutes par nos formateurs.</p>
            </div>
            <a href="cours.php" class="btn btn-outline btn-sm">Voir tout le catalogue</a>
        </div>

        <?php if ($coursRecents === []): ?>
            <div class="empty-state"><h3>Aucun cours publie pour le moment</h3></div>
        <?php else: ?>
            <div class="grid grid-cols-3">
                <?php foreach ($coursRecents as $cours): ?>
                    <article class="card course-card">
                        <div class="course-thumb"><span><?= e($cours['categorie_nom']) ?></span></div>
                        <div class="card-body">
                            <h3><a href="cours_detail.php?id=<?= (int) $cours['id'] ?>"><?= e($cours['titre']) ?></a></h3>
                            <div class="course-meta">
                                <span><?= e(niveau_label($cours['niveau'])) ?></span>
                                <span>&middot;</span>
                                <span><?= e($cours['formateur_prenom'] . ' ' . $cours['formateur_nom']) ?></span>
                            </div>
                            <p class="course-desc"><?= e(mb_strimwidth($cours['description'], 0, 110, '...')) ?></p>
                            <div class="card-footer">
                                <a href="cours_detail.php?id=<?= (int) $cours['id'] ?>" class="btn btn-primary btn-sm">Voir le cours</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container grid grid-cols-3">
        <div class="card card-body">
            <h3>Pour les administrateurs</h3>
            <p class="text-muted">Gerez les comptes, les categories et suivez l'engagement global de la plateforme.</p>
        </div>
        <div class="card card-body">
            <h3>Pour les formateurs</h3>
            <p class="text-muted">Creez vos cours, organisez-les en modules et ajoutez documents, videos et quiz.</p>
        </div>
        <div class="card card-body">
            <h3>Pour les etudiants</h3>
            <p class="text-muted">Recherchez un cours, inscrivez-vous en un clic et suivez votre progression module par module.</p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
