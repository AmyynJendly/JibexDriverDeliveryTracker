<?php $estModification = $categorie !== null; ?>
<div class="breadcrumb">
    <a href="<?= e(url('/admin/categories')) ?>">Categories</a> / <?= $estModification ? 'Modifier' : 'Ajouter' ?>
</div>

<div class="card" style="max-width:560px;">
    <div class="card-body">
        <form method="post"
              action="<?= e($estModification ? url('/admin/categories/' . $categorie['id'] . '/modifier') : url('/admin/categories/creer')) ?>"
              novalidate data-validate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="nom">Nom de la categorie</label>
                <input type="text" id="nom" name="nom" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'nom') ?>" data-rule="required|max:100">
                <?php if (isset($errors['nom'])): ?><p class="form-error"><?= e($errors['nom']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description (optionnel)</label>
                <textarea id="description" name="description" class="form-control" rows="3"><?= old($old, 'description') ?></textarea>
            </div>

            <div class="cluster" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary"><?= $estModification ? 'Enregistrer' : 'Creer la categorie' ?></button>
                <a href="<?= e(url('/admin/categories')) ?>" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>
