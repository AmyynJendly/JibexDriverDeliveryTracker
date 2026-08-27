<div class="section-head">
    <div>
        <h2 style="margin:0;">Categories de cours</h2>
        <p class="text-muted" style="margin:4px 0 0;"><?= count($categories) ?> categorie(s).</p>
    </div>
    <a href="<?= e(url('/admin/categories/creer')) ?>" class="btn btn-primary">Ajouter une categorie</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Cours rattaches</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($categories === []): ?>
                    <tr><td colspan="4" class="text-soft">Aucune categorie pour le moment.</td></tr>
                <?php endif; ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><strong><?= e($cat['nom']) ?></strong></td>
                        <td class="text-muted"><?= e($cat['description'] ?? '') ?></td>
                        <td><span class="badge badge-neutre"><?= (int) $cat['nb_cours'] ?></span></td>
                        <td class="cell-actions">
                            <a href="<?= e(url('/admin/categories/' . $cat['id'] . '/modifier')) ?>" class="btn btn-outline btn-sm">Modifier</a>
                            <form method="post" action="<?= e(url('/admin/categories/' . $cat['id'] . '/supprimer')) ?>" data-confirm="Supprimer cette categorie ?">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-sm" <?= $cat['nb_cours'] > 0 ? 'disabled title="Des cours utilisent encore cette categorie"' : '' ?>>Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
