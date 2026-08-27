<div class="auth-shell">
    <div class="card auth-card">
        <h1>Connexion</h1>
        <p class="text-muted" style="margin-bottom:26px;">Accedez a votre espace Skolea.</p>

        <form method="post" action="<?= e(url('/connexion')) ?>" novalidate data-validate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="email">Adresse email</label>
                <input type="email" id="email" name="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'email') ?>" data-rule="required|email" autocomplete="email">
                <?php if (isset($errors['email'])): ?><p class="form-error"><?= e($errors['email']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control<?= isset($errors['mot_de_passe']) ? ' is-invalid' : '' ?>"
                       data-rule="required" autocomplete="current-password">
                <?php if (isset($errors['mot_de_passe'])): ?><p class="form-error"><?= e($errors['mot_de_passe']) ?></p><?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>

        <p class="text-muted" style="margin-top:22px;text-align:center;">
            Pas encore de compte ? <a href="<?= e(url('/inscription')) ?>" style="color:var(--color-primary);font-weight:600;">Inscrivez-vous</a>
        </p>

        <div class="card-body" style="margin-top:8px;padding:14px 16px;background:var(--color-surface-alt);border-radius:var(--radius-sm);">
            <p class="text-soft" style="margin:0;font-size:.78rem;">
                Compte de demonstration : <strong>admin@skolea.tn</strong> / etudiant : <strong>rania.ferjani@skolea.tn</strong>
                &mdash; mot de passe <strong>Passer123!</strong>
            </p>
        </div>
    </div>
</div>
