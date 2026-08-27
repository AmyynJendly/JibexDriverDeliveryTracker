<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Utilisateur.php';
require_once __DIR__ . '/../../Controller/UtilisateurController.php';

if (!a_le_role('administrateur')) {
    flash_set('erreur', 'Acces reserve aux administrateurs.');
    header('Location: ' . (est_connecte() ? '../FrontOffice/index.php' : '../FrontOffice/connexion.php'));
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$utilisateur = (new Utilisateur())->findById($id);
if (!$utilisateur) {
    flash_set('erreur', 'Utilisateur introuvable.');
    header('Location: admin_utilisateurs.php');
    exit;
}

$old = $utilisateur;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['_csrf'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(419);
        die('Session expiree, merci de recharger la page.');
    }

    $data = [
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'role' => $_POST['role'] ?? 'etudiant',
        'bio' => trim($_POST['bio'] ?? ''),
        'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
    ];

    if ($id === (int) $_SESSION['utilisateur']['id'] && $data['role'] !== 'administrateur') {
        flash_set('erreur', "Vous ne pouvez pas retirer votre propre role d'administrateur.");
        header('Location: admin_utilisateur_modifier.php?id=' . $id);
        exit;
    }

    $validator = (new UtilisateurController())->modifierParAdmin($id, $data);

    if ($validator->fails()) {
        $old = $data;
        $errors = $validator->errors();
    } else {
        if ($id === (int) $_SESSION['utilisateur']['id']) {
            $_SESSION['utilisateur']['nom'] = $data['nom'];
            $_SESSION['utilisateur']['prenom'] = $data['prenom'];
        }
        flash_set('succes', 'Utilisateur mis a jour avec succes.');
        header('Location: admin_utilisateurs.php');
        exit;
    }
}

$pageTitle = 'Modifier un utilisateur';
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
    <a href="admin_utilisateurs.php">Utilisateurs</a> / Modifier
</div>

<div class="card" style="max-width:640px;">
    <div class="card-body">
        <form method="post" action="admin_utilisateur_modifier.php?id=<?= $id ?>" novalidate data-validate>
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="prenom">Prenom</label>
                    <input type="text" id="prenom" name="prenom" class="form-control<?= isset($errors['prenom']) ? ' is-invalid' : '' ?>"
                           value="<?= old($old, 'prenom') ?>" placeholder="Ex : Sonia" data-rule="required|min:3|max:80|alpha">
                    <?php if (isset($errors['prenom'])): ?><p class="form-error"><?= e($errors['prenom']) ?></p><?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label" for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>"
                           value="<?= old($old, 'nom') ?>" placeholder="Ex : Trabelsi" data-rule="required|min:3|max:80|alpha">
                    <?php if (isset($errors['nom'])): ?><p class="form-error"><?= e($errors['nom']) ?></p><?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'email') ?>" placeholder="exemple@skolea.tn" data-rule="required|email">
                <?php if (isset($errors['email'])): ?><p class="form-error"><?= e($errors['email']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="role">Role</label>
                <select id="role" name="role" class="form-control">
                    <?php foreach (['administrateur', 'formateur', 'etudiant'] as $r): ?>
                        <option value="<?= e($r) ?>" <?= ($old['role'] ?? 'etudiant') === $r ? 'selected' : '' ?>><?= e(role_label($r)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="bio">Bio (optionnel)</label>
                <textarea id="bio" name="bio" class="form-control" rows="3" placeholder="Quelques mots sur cette personne"><?= old($old, 'bio') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="mot_de_passe">Mot de passe (laisser vide pour ne pas changer)</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control<?= isset($errors['mot_de_passe']) ? ' is-invalid' : '' ?>"
                       placeholder="8 caracteres minimum" data-rule="min:8" autocomplete="new-password">
                <?php if (isset($errors['mot_de_passe'])): ?><p class="form-error"><?= e($errors['mot_de_passe']) ?></p><?php endif; ?>
            </div>

            <div class="cluster" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="admin_utilisateurs.php" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
