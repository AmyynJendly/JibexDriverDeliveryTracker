<?php $estModification = $utilisateur !== null; ?>
<div class="breadcrumb">
    <a href="<?= e(url('/admin/utilisateurs')) ?>">Utilisateurs</a> / <?= $estModification ? 'Modifier' : 'Ajouter' ?>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-body">
        <form method="post"
              action="<?= e($estModification ? url('/admin/utilisateurs/' . $utilisateur['id'] . '/modifier') : url('/admin/utilisateurs/creer')) ?>"
              novalidate data-validate>
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
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'email') ?>" data-rule="required|email">
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
                <textarea id="bio" name="bio" class="form-control" rows="3"><?= old($old, 'bio') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="mot_de_passe">
                    Mot de passe <?= $estModification ? '(laisser vide pour ne pas changer)' : '' ?>
                </label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control<?= isset($errors['mot_de_passe']) ? ' is-invalid' : '' ?>"
                       data-rule="<?= $estModification ? 'min:8' : 'required|min:8' ?>" autocomplete="new-password">
                <?php if (isset($errors['mot_de_passe'])): ?><p class="form-error"><?= e($errors['mot_de_passe']) ?></p><?php endif; ?>
            </div>

            <div class="cluster" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary"><?= $estModification ? 'Enregistrer' : 'Creer le compte' ?></button>
                <a href="<?= e(url('/admin/utilisateurs')) ?>" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>
