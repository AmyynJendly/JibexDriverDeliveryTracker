<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Cours.php';
require_once __DIR__ . '/../../Model/Module.php';
require_once __DIR__ . '/../../Model/Inscription.php';
require_once __DIR__ . '/../../Controller/InscriptionController.php';

$id = (int) ($_GET['id'] ?? 0);
$coursModel = new Cours();
$cours = $coursModel->find($id);

if (!$cours || $cours['statut'] !== 'publie') {
    http_response_code(404);
    $pageTitle = 'Cours introuvable';
    require __DIR__ . '/includes/header.php';
    echo '<div class="container section"><div class="empty-state"><h1 style="font-size:3.5rem;">404</h1><h3>Ce cours est introuvable.</h3><a href="cours.php" class="btn btn-primary">Retour au catalogue</a></div></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$modules = (new Module())->byCours($cours['id']);

$utilisateur = utilisateur_connecte();
$inscription = null;
if ($utilisateur && $utilisateur['role'] === 'etudiant') {
    $inscription = (new Inscription())->findByEtudiantEtCours($utilisateur['id'], $cours['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'inscrire') {
    if (!est_connecte() || $_SESSION['utilisateur']['role'] !== 'etudiant') {
        flash_set('erreur', 'Seuls les etudiants peuvent s\'inscrire a un cours.');
        header('Location: cours_detail.php?id=' . $id);
        exit;
    }
    $token = $_POST['_csrf'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(419);
        die('Session expiree, merci de recharger la page.');
    }

    (new InscriptionController())->inscrire($utilisateur['id'], $cours['id']);
    flash_set('succes', 'Inscription reussie ! Vous pouvez commencer le cours.');
    header('Location: ../FrontOffice/suivre_cours.php?id=' . $cours['id']);
    exit;
}

$pageTitle = $cours['titre'];
require __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-bottom:24px;">
    <div class="container">
        <div class="breadcrumb">
            <a href="cours.php">Catalogue</a> / <?= e($cours['titre']) ?>
        </div>

        <div class="grid" style="grid-template-columns:1.7fr 1fr;align-items:start;">
            <div>
                <div class="tag-list" style="margin-bottom:14px;">
                    <span class="badge badge-primaire"><?= e($cours['categorie_nom']) ?></span>
                    <span class="badge badge-neutre"><?= e(niveau_label($cours['niveau'])) ?></span>
                </div>
                <h1><?= e($cours['titre']) ?></h1>
                <p class="text-muted"><?= nl2br(e($cours['description'])) ?></p>

                <div class="cluster" style="margin:24px 0;">
                    <span class="user-avatar"><?= e(mb_strtoupper(mb_substr($cours['formateur_prenom'], 0, 1) . mb_substr($cours['formateur_nom'], 0, 1))) ?></span>
                    <div>
                        <strong style="display:block;font-size:.9rem;"><?= e($cours['formateur_prenom'] . ' ' . $cours['formateur_nom']) ?></strong>
                        <span class="text-soft" style="font-size:.8rem;">Formateur</span>
                    </div>
                    <span class="spacer"></span>
                    <span class="text-soft" style="font-size:.85rem;"><?= (int) $cours['nb_inscrits'] ?> etudiant(s) inscrit(s)</span>
                </div>

                <div class="card">
                    <div class="card-header"><h3 style="margin:0;">Contenu du cours</h3></div>
                    <div class="card-body">
                        <?php if ($modules === []): ?>
                            <p class="text-soft">Le contenu de ce cours sera bientot disponible.</p>
                        <?php endif; ?>
                        <?php foreach ($modules as $module): ?>
                            <div class="module-item">
                                <strong>#<?= (int) $module['ordre'] ?> &middot; <?= e($module['titre']) ?></strong>
                                <?php if ($module['description']): ?>
                                    <p class="text-muted" style="margin:6px 0 0;font-size:.86rem;"><?= e($module['description']) ?></p>
                                <?php endif; ?>
                                <p class="text-soft" style="margin:8px 0 0;font-size:.78rem;"><?= (int) $module['nb_ressources'] ?> ressource(s)</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 style="margin-top:0;">Rejoindre ce cours</h3>
                    <p class="text-muted" style="font-size:.9rem;"><?= count($modules) ?> module(s) au programme.</p>

                    <?php if ($inscription): ?>
                        <p class="text-muted" style="font-size:.85rem;">Vous suivez deja ce cours.</p>
                        <a href="suivre_cours.php?id=<?= (int) $cours['id'] ?>" class="btn btn-primary btn-block">Continuer le cours</a>
                    <?php elseif ($utilisateur && $utilisateur['role'] === 'etudiant'): ?>
                        <form method="post" action="cours_detail.php?id=<?= (int) $cours['id'] ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="inscrire">
                            <button type="submit" class="btn btn-primary btn-block">S'inscrire gratuitement</button>
                        </form>
                    <?php elseif ($utilisateur): ?>
                        <p class="text-soft" style="font-size:.85rem;">Seuls les comptes etudiant peuvent s'inscrire a un cours.</p>
                    <?php else: ?>
                        <a href="connexion.php" class="btn btn-primary btn-block">Se connecter pour s'inscrire</a>
                        <p class="text-soft" style="font-size:.8rem;margin-top:10px;">
                            Pas encore de compte ? <a href="inscription.php" style="color:var(--color-primary);font-weight:600;">Inscrivez-vous</a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
