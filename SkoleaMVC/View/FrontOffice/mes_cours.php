<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Inscription.php';

if (!a_le_role('etudiant')) {
    flash_set('erreur', 'Cette page est reservee aux etudiants.');
    header('Location: ' . (est_connecte() ? 'index.php' : 'connexion.php'));
    exit;
}

$etudiantId = (int) $_SESSION['utilisateur']['id'];
$inscriptionModel = new Inscription();

$parPage = 6;
$page = max(1, (int) ($_GET['page'] ?? 1));
$total = $inscriptionModel->countByEtudiant($etudiantId);
$totalPages = max(1, (int) ceil($total / $parPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $parPage;
$inscriptions = $inscriptionModel->paginateByEtudiant($etudiantId, $parPage, $offset);

$pageTitle = 'Mes cours';
require __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1>Mes cours</h1>
        <p class="text-muted"><?= (int) $total ?> cours suivi(s).</p>

        <?php if ($inscriptions === []): ?>
            <div class="empty-state card">
                <h3>Vous n'etes inscrit a aucun cours</h3>
                <p>Parcourez le catalogue pour trouver votre premier cours.</p>
                <a href="cours.php" class="btn btn-primary">Voir le catalogue</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-3">
                <?php foreach ($inscriptions as $i): ?>
                    <article class="card course-card">
                        <div class="course-thumb"><span><?= e($i['categorie_nom']) ?></span></div>
                        <div class="card-body">
                            <h3><a href="suivre_cours.php?id=<?= (int) $i['cours_id'] ?>"><?= e($i['cours_titre']) ?></a></h3>
                            <div class="course-meta">
                                <?php if ($i['statut'] === 'termine'): ?>
                                    <span class="badge badge-succes">Termine</span>
                                <?php elseif ($i['statut'] === 'abandonne'): ?>
                                    <span class="badge badge-danger">Abandonne</span>
                                <?php else: ?>
                                    <span class="badge badge-attente">En cours</span>
                                <?php endif; ?>
                                <span><?= e($i['formateur_prenom'] . ' ' . $i['formateur_nom']) ?></span>
                            </div>
                            <div style="margin-top:6px;">
                                <div class="progress-bar"><span style="width:<?= (int) $i['progression'] ?>%"></span></div>
                                <p class="text-soft" style="margin:6px 0 0;font-size:.78rem;"><?= (int) $i['progression'] ?>% termine</p>
                            </div>
                            <div class="card-footer">
                                <a href="suivre_cours.php?id=<?= (int) $i['cours_id'] ?>" class="btn btn-primary btn-sm">Continuer</a>
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
