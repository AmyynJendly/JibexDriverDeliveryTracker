<div class="auth-shell">
    <div class="card auth-card">
        <h1>Creer un compte</h1>
        <p class="text-muted" style="margin-bottom:26px;">Inscrivez-vous en tant qu'etudiant pour rejoindre des cours.</p>

        <form method="post" action="<?= e(url('/inscription')) ?>" novalidate data-validate>
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="prenom">Prenom</label>
                    <input type="text" id="prenom" name="prenom" class="form-control<?= isset($errors['prenom']) ? ' is-invalid' : '' ?>"
                           value="<?= old($old, 'prenom') ?>" data-rule="required|max:80" autocomplete="given-name">
                    <?php if (isset($errors['prenom'])): ?><p class="form-error"><?= e($errors['prenom']) ?></p><?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label" for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>"
                           value="<?= old($old, 'nom') ?>" data-rule="required|max:80" autocomplete="family-name">
                    <?php if (isset($errors['nom'])): ?><p class="form-error"><?= e($errors['nom']) ?></p><?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Adresse email</label>
                <input type="email" id="email" name="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'email') ?>" data-rule="required|email" autocomplete="email">
                <?php if (isset($errors['email'])): ?><p class="form-error"><?= e($errors['email']) ?></p><?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control<?= isset($errors['mot_de_passe']) ? ' is-invalid' : '' ?>"
                           data-rule="required|min:8" autocomplete="new-password">
                    <p class="form-hint">8 caracteres minimum.</p>
                    <?php if (isset($errors['mot_de_passe'])): ?><p class="form-error"><?= e($errors['mot_de_passe']) ?></p><?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label" for="mot_de_passe_confirmation">Confirmation</label>
                    <input type="password" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation"
                           class="form-control<?= isset($errors['mot_de_passe_confirmation']) ? ' is-invalid' : '' ?>"
                           data-rule="required|matches:mot_de_passe" autocomplete="new-password">
                    <?php if (isset($errors['mot_de_passe_confirmation'])): ?><p class="form-error"><?= e($errors['mot_de_passe_confirmation']) ?></p><?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Creer mon compte</button>
        </form>

        <p class="text-muted" style="margin-top:22px;text-align:center;">
            Deja inscrit ? <a href="<?= e(url('/connexion')) ?>" style="color:var(--color-primary);font-weight:600;">Connectez-vous</a>
        </p>
    </div>
</div>
