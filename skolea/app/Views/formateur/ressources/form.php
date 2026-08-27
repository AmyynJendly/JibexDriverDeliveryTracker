<?php $estModification = $ressource !== null; ?>
<div class="breadcrumb">
    <a href="<?= e(url('/formateur/cours')) ?>">Mes cours</a> /
    <a href="<?= e(url('/formateur/cours/' . $module['cours_id'])) ?>">Cours</a> /
    <?= $estModification ? 'Modifier la ressource' : 'Ajouter une ressource' ?>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <p class="text-muted" style="margin-top:0;">Module : <strong><?= e($module['titre']) ?></strong></p>

        <form method="post"
              action="<?= e($estModification ? url('/formateur/ressources/' . $ressource['id'] . '/modifier') : url('/formateur/modules/' . $module['id'] . '/ressources/creer')) ?>"
              enctype="multipart/form-data" novalidate data-validate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="titre">Titre de la ressource</label>
                <input type="text" id="titre" name="titre" class="form-control<?= isset($errors['titre']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'titre') ?>" data-rule="required|max:150">
                <?php if (isset($errors['titre'])): ?><p class="form-error"><?= e($errors['titre']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="type">Type de ressource</label>
                <select id="type" name="type" class="form-control">
                    <?php foreach (['document', 'video', 'quiz'] as $t): ?>
                        <option value="<?= e($t) ?>" <?= ($old['type'] ?? 'document') === $t ? 'selected' : '' ?>><?= e(type_ressource_label($t)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="fichier">Fichier a televerser (pour un document)</label>
                <input type="file" id="fichier" name="fichier" class="form-control<?= isset($errors['fichier']) ? ' is-invalid' : '' ?>"
                       accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.csv">
                <p class="form-hint">PDF, Word, PowerPoint, ZIP ou CSV, 8 Mo maximum.</p>
                <?php if (isset($errors['fichier'])): ?><p class="form-error"><?= e($errors['fichier']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="contenu">Ou une URL (pour une video ou un quiz externe)</label>
                <input type="text" id="contenu" name="contenu" class="form-control<?= isset($errors['contenu']) ? ' is-invalid' : '' ?>"
                       value="<?= old($old, 'contenu') ?>" placeholder="https://...">
                <?php if (isset($errors['contenu'])): ?><p class="form-error"><?= e($errors['contenu']) ?></p><?php endif; ?>
                <?php if ($estModification && !empty($ressource['contenu'])): ?>
                    <p class="form-hint">Contenu actuel : <?= e($ressource['contenu']) ?> (laisser vide pour le conserver)</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description (optionnel)</label>
                <textarea id="description" name="description" class="form-control" rows="3"><?= old($old, 'description') ?></textarea>
            </div>

            <div class="cluster" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary"><?= $estModification ? 'Enregistrer' : 'Ajouter la ressource' ?></button>
                <a href="<?= e(url('/formateur/cours/' . $module['cours_id'])) ?>" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>
