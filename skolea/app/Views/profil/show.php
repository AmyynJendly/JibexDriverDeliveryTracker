<?php $isBack = in_array(\App\Core\Auth::role(), ['administrateur', 'formateur'], true); ?>
<div class="<?= $isBack ? '' : 'container section' ?>" style="<?= $isBack ? '' : 'max-width:640px;' ?>">
    <?php if (!$isBack): ?><h1>Mon profil</h1><?php endif; ?>

    <div class="card" style="margin-bottom:24px;">
        <div class="card-header"><h3 style="margin:0;">Informations personnelles</h3></div>
        <div class="card-body">
            <div class="cluster" style="margin-bottom:20px;">
                <span class="avatar-lg"><?= e(mb_strtoupper(mb_substr($utilisateur['prenom'], 0, 1) . mb_substr($utilisateur['nom'], 0, 1))) ?></span>
                <div>
                    <strong style="display:block;"><?= e($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></strong>
                    <span class="text-soft"><?= e($utilisateur['email']) ?> &middot; <?= e(role_label($utilisateur['role'])) ?></span>
                </div>
            </div>

            <form method="post" action="<?= e(url('/profil')) ?>" novalidate data-validate>
                <?= csrf_field() ?>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="prenom">Prenom</label>
                        <input type="text" id="prenom" name="prenom" class="form-control<?= isset($errors['prenom']) ? ' is-invalid' : '' ?>"
                               value="<?= old($old, 'prenom') ?>" data-rule="required|max:80">
                        <?php if (isset($errors['prenom'])): ?><p class="form-error"><?= e($errors['prenom']) ?></p><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>"
                               value="<?= old($old, 'nom') ?>" data-rule="required|max:80">
                        <?php if (isset($errors['nom'])): ?><p class="form-error"><?= e($errors['nom']) ?></p><?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="bio">Bio</label>
                    <textarea id="bio" name="bio" class="form-control" rows="3"><?= old($old, 'bio') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Changer le mot de passe</h3></div>
        <div class="card-body">
            <form method="post" action="<?= e(url('/profil/mot-de-passe')) ?>" novalidate data-validate>
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label" for="mot_de_passe_actuel">Mot de passe actuel</label>
                    <input type="password" id="mot_de_passe_actuel" name="mot_de_passe_actuel"
                           class="form-control<?= isset($erreursMotDePasse['mot_de_passe_actuel']) ? ' is-invalid' : '' ?>" data-rule="required">
                    <?php if (isset($erreursMotDePasse['mot_de_passe_actuel'])): ?><p class="form-error"><?= e($erreursMotDePasse['mot_de_passe_actuel']) ?></p><?php endif; ?>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="mot_de_passe">Nouveau mot de passe</label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe"
                               class="form-control<?= isset($erreursMotDePasse['mot_de_passe']) ? ' is-invalid' : '' ?>" data-rule="required|min:8">
                        <?php if (isset($erreursMotDePasse['mot_de_passe'])): ?><p class="form-error"><?= e($erreursMotDePasse['mot_de_passe']) ?></p><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="mot_de_passe_confirmation">Confirmation</label>
                        <input type="password" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation"
                               class="form-control<?= isset($erreursMotDePasse['mot_de_passe_confirmation']) ? ' is-invalid' : '' ?>" data-rule="required|matches:mot_de_passe">
                        <?php if (isset($erreursMotDePasse['mot_de_passe_confirmation'])): ?><p class="form-error"><?= e($erreursMotDePasse['mot_de_passe_confirmation']) ?></p><?php endif; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-outline">Changer le mot de passe</button>
            </form>
        </div>
    </div>
</div>
