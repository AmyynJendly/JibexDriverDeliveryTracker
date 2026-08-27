<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Model/Utilisateur.php';
require_once __DIR__ . '/../../Controller/UtilisateurController.php';

// Deja connecte : on renvoie directement vers son espace.
if (est_connecte()) {
    $role = $_SESSION['utilisateur']['role'];
    if ($role === 'administrateur') {
        header('Location: ../BackOffice/admin_dashboard.php');
    } elseif ($role === 'formateur') {
        header('Location: ../BackOffice/formateur_dashboard.php');
    } else {
        header('Location: mes_cours.php');
    }
    exit;
}

$old = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['_csrf'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(419);
        die('Session expiree, merci de recharger la page.');
    }

    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    $controller = new UtilisateurController();
    list($utilisateur, $validator) = $controller->connecter($email, $motDePasse);

    if ($validator->fails()) {
        $old = ['email' => $email];
        $errors = $validator->errors();
    } else {
        $_SESSION['utilisateur'] = [
            'id' => (int) $utilisateur['id'],
            'nom' => $utilisateur['nom'],
            'prenom' => $utilisateur['prenom'],
            'email' => $utilisateur['email'],
            'role' => $utilisateur['role'],
        ];
        flash_set('succes', 'Bienvenue, ' . $utilisateur['prenom'] . ' !');

        if ($utilisateur['role'] === 'administrateur') {
            header('Location: ../BackOffice/admin_dashboard.php');
        } elseif ($utilisateur['role'] === 'formateur') {
            header('Location: ../BackOffice/formateur_dashboard.php');
        } else {
            header('Location: mes_cours.php');
        }
        exit;
    }
}

$pageTitle = 'Connexion';
require __DIR__ . '/includes/header.php';
?>

<div class="auth-shell">
    <div class="card auth-card">
        <h1>Connexion</h1>
        <p class="text-muted" style="margin-bottom:26px;">Accedez a votre espace Skolea.</p>

        <form method="post" action="connexion.php" novalidate data-validate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="email">Adresse email</label>
                <input type="email" id="email" name="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'email') ?>" placeholder="exemple@skolea.tn" data-rule="required|email" autocomplete="email">
                <?php if (isset($errors['email'])): ?><p class="form-error"><?= e($errors['email']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control<?= isset($errors['mot_de_passe']) ? ' is-invalid' : '' ?>"
                       placeholder="Votre mot de passe" data-rule="required" autocomplete="current-password">
                <?php if (isset($errors['mot_de_passe'])): ?><p class="form-error"><?= e($errors['mot_de_passe']) ?></p><?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>

        <p class="text-muted" style="margin-top:22px;text-align:center;">
            Pas encore de compte ? <a href="inscription.php" style="color:var(--color-primary);font-weight:600;">Inscrivez-vous</a>
        </p>

        <div class="card-body" style="margin-top:8px;padding:14px 16px;background:var(--color-surface-alt);border-radius:var(--radius-sm);">
            <p class="text-soft" style="margin:0;font-size:.78rem;">
                Compte de demonstration : <strong>admin@skolea.tn</strong> / etudiant : <strong>rania.ferjani@skolea.tn</strong>
                &mdash; mot de passe <strong>Passer123!</strong>
            </p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
