<?php $estModification = $module !== null; ?>
<div class="breadcrumb">
    <a href="<?= e(url('/formateur/cours')) ?>">Mes cours</a> /
    <a href="<?= e(url('/formateur/cours/' . $cours['id'])) ?>"><?= e($cours['titre']) ?></a> /
    <?= $estModification ? 'Modifier le module' : 'Ajouter un module' ?>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form method="post"
              action="<?= e($estModification ? url('/formateur/modules/' . $module['id'] . '/modifier') : url('/formateur/cours/' . $cours['id'] . '/modules/creer')) ?>"
              novalidate data-validate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="titre">Titre du module</label>
                <input type="text" id="titre" name="titre" class="form-control<?= isset($errors['titre']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'titre') ?>" data-rule="required|max:150">
                <?php if (isset($errors['titre'])): ?><p class="form-error"><?= e($errors['titre']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description (optionnel)</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?= old($old, 'description') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="ordre">Ordre d'affichage</label>
                <input type="number" id="ordre" name="ordre" min="1" class="form-control<?= isset($errors['ordre']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'ordre') ?>" placeholder="Laisser vide pour l'ajouter a la fin">
                <?php if (isset($errors['ordre'])): ?><p class="form-error"><?= e($errors['ordre']) ?></p><?php endif; ?>
            </div>

            <div class="cluster" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary"><?= $estModification ? 'Enregistrer' : 'Ajouter le module' ?></button>
                <a href="<?= e(url('/formateur/cours/' . $cours['id'])) ?>" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>
