<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Cours.php';
require_once __DIR__ . '/../../Model/Categorie.php';
require_once __DIR__ . '/../../Controller/CoursController.php';

if (!a_le_role('formateur')) {
    flash_set('erreur', 'Acces reserve aux formateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$formateurId = (int) $_SESSION['utilisateur']['id'];
$id = (int) ($_GET['id'] ?? 0);
$cours = (new Cours())->find($id);

if (!$cours || (int) $cours['formateur_id'] !== $formateurId) {
    flash_set('erreur', "Ce cours n'existe pas ou ne vous appartient pas.");
    header('Location: formateur_cours.php');
    exit;
}

$old = $cours;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['_csrf'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(419);
        die('Session expiree, merci de recharger la page.');
    }

    $data = [
        'titre' => trim($_POST['titre'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'categorie_id' => trim($_POST['categorie_id'] ?? ''),
        'niveau' => $_POST['niveau'] ?? 'debutant',
        'statut' => $_POST['statut'] ?? 'brouillon',
    ];

    $validator = (new CoursController())->modifier($id, $data, $_FILES['image'] ?? []);

    if ($validator->fails()) {
        $old = $data;
        $errors = $validator->errors();
    } else {
        flash_set('succes', 'Cours mis a jour avec succes.');
        header('Location: formateur_cours_show.php?id=' . $id);
        exit;
    }
}

$categories = (new Categorie())->all();
$pageTitle = 'Modifier le cours';
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
    <a href="formateur_cours.php">Mes cours</a> / Modifier
</div>

<div class="card" style="max-width:680px;">
    <div class="card-body">
        <form method="post" action="formateur_cours_modifier.php?id=<?= $id ?>" enctype="multipart/form-data" id="form-cours" novalidate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="titre">Titre du cours</label>
                <input type="text" id="titre" name="titre" class="form-control<?= isset($errors['titre']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'titre') ?>" placeholder="Ex : Introduction a PHP 8">
                <p id="titre-message" class="<?= isset($errors['titre']) ? 'form-error' : '' ?>"><?= isset($errors['titre']) ? e($errors['titre']) : '' ?></p>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>"
                          rows="5" placeholder="Decrivez le contenu et les objectifs du cours"><?= old($old, 'description') ?></textarea>
                <p id="description-message" class="<?= isset($errors['description']) ? 'form-error' : '' ?>"><?= isset($errors['description']) ? e($errors['description']) : '' ?></p>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="categorie_id">Categorie</label>
                    <select id="categorie_id" name="categorie_id" class="form-control<?= isset($errors['categorie_id']) ? ' is-invalid' : '' ?>">
                        <option value="">Choisir...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int) $cat['id'] ?>" <?= (string) ($old['categorie_id'] ?? '') === (string) $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p id="categorie_id-message" class="<?= isset($errors['categorie_id']) ? 'form-error' : '' ?>"><?= isset($errors['categorie_id']) ? e($errors['categorie_id']) : '' ?></p>
                </div>
                <div class="form-group">
                    <label class="form-label" for="niveau">Niveau</label>
                    <select id="niveau" name="niveau" class="form-control">
                        <?php foreach (['debutant', 'intermediaire', 'avance'] as $n): ?>
                            <option value="<?= e($n) ?>" <?= ($old['niveau'] ?? 'debutant') === $n ? 'selected' : '' ?>><?= e(niveau_label($n)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="statut">Statut</label>
                    <select id="statut" name="statut" class="form-control">
                        <option value="brouillon" <?= ($old['statut'] ?? 'brouillon') === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                        <option value="publie" <?= ($old['statut'] ?? '') === 'publie' ? 'selected' : '' ?>>Publie</option>
                    </select>
                    <p id="statut-message" class="form-hint">Un cours en brouillon n'apparait pas dans le catalogue public.</p>
                </div>
                <div class="form-group">
                    <label class="form-label" for="image">Image du cours (optionnel)</label>
                    <input type="file" id="image" name="image" class="form-control<?= isset($errors['image']) ? ' is-invalid' : '' ?>" accept=".jpg,.jpeg,.png,.webp">
                    <p class="form-hint">JPG, PNG ou WEBP, 3 Mo maximum. Laisser vide pour conserver l'image actuelle.</p>
                    <?php if (isset($errors['image'])): ?><p class="form-error"><?= e($errors['image']) ?></p><?php endif; ?>
                </div>
            </div>

            <div class="cluster" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary" onclick="return validerFormulaire()">Enregistrer</button>
                <a href="formateur_cours_show.php?id=<?= $id ?>" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script src="../../assets/js/cours-validation.js"></script>

<?php require __DIR__ . '/includes/footer.php'; ?>
