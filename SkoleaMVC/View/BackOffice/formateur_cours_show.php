<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Cours.php';
require_once __DIR__ . '/../../Model/Module.php';
require_once __DIR__ . '/../../Model/Ressource.php';
require_once __DIR__ . '/../../Model/Inscription.php';
require_once __DIR__ . '/../../Controller/CoursController.php';

if (!a_le_role('formateur')) {
    flash_set('erreur', 'Acces reserve aux formateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$formateurId = (int) $_SESSION['utilisateur']['id'];
$coursModel = new Cours();
$id = (int) ($_GET['id'] ?? 0);
$cours = $coursModel->find($id);

if (!$cours || (int) $cours['formateur_id'] !== $formateurId) {
    flash_set('erreur', "Ce cours n'existe pas ou ne vous appartient pas.");
    header('Location: formateur_cours.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer_cours') {
    $token = $_POST['_csrf'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(419);
        die('Session expiree, merci de recharger la page.');
    }

    (new CoursController())->supprimer($id);
    flash_set('succes', 'Cours supprime avec succes.');
    header('Location: formateur_cours.php');
    exit;
}

$moduleModel = new Module();
$modules = $moduleModel->byCours($id);

$ressourceModel = new Ressource();
$ressourcesParModule = [];
foreach ($modules as $module) {
    $ressourcesParModule[$module['id']] = $ressourceModel->byModule($module['id']);
}

$participants = (new Inscription())->byCours($id);

$pageTitle = $cours['titre'];
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
    <a href="formateur_cours.php">Mes cours</a> / <?= e($cours['titre']) ?>
</div>

<div class="section-head">
    <div>
        <h2 style="margin:0;"><?= e($cours['titre']) ?></h2>
        <div class="cluster" style="margin-top:8px;">
            <?php if ($cours['statut'] === 'publie'): ?>
                <span class="badge badge-succes">Publie</span>
            <?php else: ?>
                <span class="badge badge-attente">Brouillon</span>
            <?php endif; ?>
            <span class="badge badge-neutre"><?= e($cours['categorie_nom']) ?></span>
            <span class="badge badge-neutre"><?= e(niveau_label($cours['niveau'])) ?></span>
        </div>
    </div>
    <div class="cluster">
        <a href="formateur_cours_modifier.php?id=<?= $id ?>" class="btn btn-outline">Modifier le cours</a>
        <form method="post" action="formateur_cours_show.php?id=<?= $id ?>" data-confirm="Supprimer definitivement ce cours et tout son contenu ?">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="supprimer_cours">
            <button type="submit" class="btn btn-danger">Supprimer</button>
        </form>
    </div>
</div>

<div class="stat-grid" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card"><span class="stat-value"><?= count($modules) ?></span><span class="stat-label">Modules</span></div>
    <div class="stat-card"><span class="stat-value"><?= count($participants) ?></span><span class="stat-label">Etudiants inscrits</span></div>
    <div class="stat-card"><span class="stat-value"><?= (int) array_sum(array_column($modules, 'nb_ressources')) ?></span><span class="stat-label">Ressources</span></div>
</div>

<div class="grid" style="grid-template-columns:1.6fr 1fr;align-items:start;">
    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;">Modules</h3>
            <a href="formateur_module_ajouter.php?cours_id=<?= $id ?>" class="btn btn-primary btn-sm">Ajouter un module</a>
        </div>
        <div class="card-body">
            <?php if ($modules === []): ?>
                <p class="text-soft">Aucun module pour le moment. Ajoutez-en un pour commencer a structurer ce cours.</p>
            <?php endif; ?>
            <?php foreach ($modules as $module): ?>
                <div class="module-item">
                    <div class="cluster" style="justify-content:space-between;">
                        <div>
                            <strong>#<?= (int) $module['ordre'] ?> &middot; <?= e($module['titre']) ?></strong>
                            <?php if ($module['description']): ?>
                                <p class="text-muted" style="margin:6px 0 0;font-size:.86rem;"><?= e($module['description']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="cluster">
                            <a href="formateur_module_modifier.php?id=<?= (int) $module['id'] ?>" class="btn btn-outline btn-sm">Modifier</a>
                            <form method="post" action="formateur_module_modifier.php" data-confirm="Supprimer ce module et ses ressources ?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id" value="<?= (int) $module['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </div>
                    </div>

                    <?php $ressources = $ressourcesParModule[$module['id']] ?? []; ?>
                    <div style="margin-top:12px;">
                        <?php foreach ($ressources as $ressource): ?>
                            <div class="resource-row">
                                <span class="resource-icon"><?= $ressource['type'] === 'video' ? '&#9654;' : ($ressource['type'] === 'quiz' ? '?' : '&#128196;') ?></span>
                                <div class="spacer">
                                    <strong style="font-size:.88rem;"><?= e($ressource['titre']) ?></strong>
                                    <div class="text-soft" style="font-size:.78rem;"><?= e(type_ressource_label($ressource['type'])) ?></div>
                                </div>
                                <a href="formateur_ressource_modifier.php?id=<?= (int) $ressource['id'] ?>" class="btn btn-outline btn-sm">Modifier</a>
                                <form method="post" action="formateur_ressource_modifier.php" data-confirm="Supprimer cette ressource ?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="supprimer">
                                    <input type="hidden" name="id" value="<?= (int) $ressource['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                        <a href="formateur_ressource_ajouter.php?module_id=<?= (int) $module['id'] ?>" class="btn btn-ghost btn-sm" style="margin-top:8px;">+ Ajouter une ressource</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Participants</h3></div>
        <div class="card-body">
            <?php if ($participants === []): ?>
                <p class="text-soft">Aucun etudiant inscrit pour le moment.</p>
            <?php endif; ?>
            <?php foreach ($participants as $p): ?>
                <div class="resource-row">
                    <span class="user-avatar"><?= e(mb_strtoupper(mb_substr($p['prenom'], 0, 1) . mb_substr($p['nom'], 0, 1))) ?></span>
                    <div class="spacer">
                        <strong style="font-size:.86rem;display:block;"><?= e($p['prenom'] . ' ' . $p['nom']) ?></strong>
                        <span class="text-soft" style="font-size:.76rem;"><?= (int) $p['progression'] ?>% termine</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
