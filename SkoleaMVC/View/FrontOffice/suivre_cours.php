<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Inscription.php';
require_once __DIR__ . '/../../Model/Module.php';
require_once __DIR__ . '/../../Model/Ressource.php';
require_once __DIR__ . '/../../Controller/InscriptionController.php';

if (!a_le_role('etudiant')) {
    flash_set('erreur', 'Cette page est reservee aux etudiants.');
    header('Location: ' . (est_connecte() ? 'index.php' : 'connexion.php'));
    exit;
}

$etudiantId = (int) $_SESSION['utilisateur']['id'];
$coursId = (int) ($_GET['id'] ?? 0);
$inscriptionModel = new Inscription();
$moduleModel = new Module();
$controller = new InscriptionController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['_csrf'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(419);
        die('Session expiree, merci de recharger la page.');
    }

    if (($_POST['action'] ?? '') === 'desinscrire') {
        $controller->desinscrire($etudiantId, $coursId);
        flash_set('info', 'Vous vous etes desinscrit de ce cours.');
        header('Location: mes_cours.php');
        exit;
    }

    if (($_POST['action'] ?? '') === 'terminer_module') {
        $moduleId = (int) ($_POST['module_id'] ?? 0);
        $controller->toggleModule($etudiantId, $coursId, $moduleId);
        header('Location: suivre_cours.php?id=' . $coursId);
        exit;
    }
}

$inscription = $inscriptionModel->trouverDetailleeParEtudiantEtCours($etudiantId, $coursId);
if (!$inscription) {
    flash_set('erreur', "Vous n'etes pas inscrit a ce cours.");
    header('Location: mes_cours.php');
    exit;
}

$modules = $moduleModel->byCours($coursId);
$ressourceModel = new Ressource();
$ressourcesParModule = [];
foreach ($modules as $module) {
    $ressourcesParModule[$module['id']] = $ressourceModel->byModule($module['id']);
}

$modulesTermines = $inscription['modules_termines'] !== null && $inscription['modules_termines'] !== ''
    ? array_map('intval', explode(',', $inscription['modules_termines']))
    : [];

$pageTitle = $inscription['cours_titre'];
require __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="breadcrumb">
            <a href="mes_cours.php">Mes cours</a> / <?= e($inscription['cours_titre']) ?>
        </div>

        <div class="section-head">
            <div>
                <h1 style="margin:0;"><?= e($inscription['cours_titre']) ?></h1>
                <p class="text-muted" style="margin:6px 0 0;">
                    Par <?= e($inscription['formateur_prenom'] . ' ' . $inscription['formateur_nom']) ?> &middot; <?= e($inscription['categorie_nom']) ?>
                </p>
            </div>
            <form method="post" action="suivre_cours.php?id=<?= $coursId ?>" data-confirm="Vous desinscrire de ce cours ? Votre progression sera perdue.">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="desinscrire">
                <button type="submit" class="btn btn-outline">Se desinscrire</button>
            </form>
        </div>

        <div class="card" style="margin-bottom:24px;">
            <div class="card-body">
                <div class="cluster" style="justify-content:space-between;margin-bottom:8px;">
                    <strong>Progression</strong>
                    <span class="text-soft"><?= (int) $inscription['progression'] ?>%</span>
                </div>
                <div class="progress-bar"><span style="width:<?= (int) $inscription['progression'] ?>%"></span></div>
            </div>
        </div>

        <?php foreach ($modules as $module): ?>
            <?php $estTermine = in_array((int) $module['id'], $modulesTermines, true); ?>
            <div class="module-item">
                <div class="cluster" style="justify-content:space-between;">
                    <div>
                        <strong>#<?= (int) $module['ordre'] ?> &middot; <?= e($module['titre']) ?></strong>
                        <?php if ($module['description']): ?>
                            <p class="text-muted" style="margin:6px 0 0;font-size:.86rem;"><?= e($module['description']) ?></p>
                        <?php endif; ?>
                    </div>
                    <form method="post" action="suivre_cours.php?id=<?= $coursId ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="terminer_module">
                        <input type="hidden" name="module_id" value="<?= (int) $module['id'] ?>">
                        <button type="submit" class="btn btn-sm <?= $estTermine ? 'btn-outline' : 'btn-primary' ?>">
                            <?= $estTermine ? 'Marque comme termine' : 'Marquer comme termine' ?>
                        </button>
                    </form>
                </div>

                <?php $ressources = $ressourcesParModule[$module['id']] ?? []; ?>
                <?php if ($ressources !== []): ?>
                    <div style="margin-top:12px;">
                        <?php foreach ($ressources as $ressource): ?>
                            <?php $href = strpos($ressource['contenu'], 'http') === 0 ? $ressource['contenu'] : '../../uploads/' . $ressource['contenu']; ?>
                            <div class="resource-row">
                                <span class="resource-icon"><?= $ressource['type'] === 'video' ? '&#9654;' : ($ressource['type'] === 'quiz' ? '?' : '&#128196;') ?></span>
                                <div class="spacer">
                                    <strong style="font-size:.88rem;"><?= e($ressource['titre']) ?></strong>
                                    <div class="text-soft" style="font-size:.78rem;"><?= e(type_ressource_label($ressource['type'])) ?></div>
                                </div>
                                <a href="<?= e($href) ?>" target="_blank" rel="noopener" class="btn btn-outline btn-sm">Consulter</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
