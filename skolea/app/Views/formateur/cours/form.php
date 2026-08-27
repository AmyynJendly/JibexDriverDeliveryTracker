<?php $estModification = $cours !== null; ?>
<div class="breadcrumb">
    <a href="<?= e(url('/formateur/cours')) ?>">Mes cours</a> / <?= $estModification ? 'Modifier' : 'Creer' ?>
</div>

<div class="card" style="max-width:680px;">
    <div class="card-body">
        <form method="post"
              action="<?= e($estModification ? url('/formateur/cours/' . $cours['id'] . '/modifier') : url('/formateur/cours/creer')) ?>"
              enctype="multipart/form-data" novalidate data-validate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="titre">Titre du cours</label>
                <input type="text" id="titre" name="titre" class="form-control<?= isset($errors['titre']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'titre') ?>" data-rule="required|max:150">
                <?php if (isset($errors['titre'])): ?><p class="form-error"><?= e($errors['titre']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>"
                          rows="5" data-rule="required"><?= old($old, 'description') ?></textarea>
                <?php if (isset($errors['description'])): ?><p class="form-error"><?= e($errors['description']) ?></p><?php endif; ?>
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
                    <?php if (isset($errors['categorie_id'])): ?><p class="form-error"><?= e($errors['categorie_id']) ?></p><?php endif; ?>
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
                    <p class="form-hint">Un cours en brouillon n'apparait pas dans le catalogue public.</p>
                </div>
                <div class="form-group">
                    <label class="form-label" for="image">Image du cours (optionnel)</label>
                    <input type="file" id="image" name="image" class="form-control<?= isset($errors['image']) ? ' is-invalid' : '' ?>" accept=".jpg,.jpeg,.png,.webp">
                    <p class="form-hint">JPG, PNG ou WEBP, 3 Mo maximum.</p>
                    <?php if (isset($errors['image'])): ?><p class="form-error"><?= e($errors['image']) ?></p><?php endif; ?>
                </div>
            </div>

            <div class="cluster" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary"><?= $estModification ? 'Enregistrer' : 'Creer le cours' ?></button>
                <a href="<?= e(url('/formateur/cours')) ?>" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>
