<div class="section-head">
    <div>
        <h2 style="margin:0;">Utilisateurs</h2>
        <p class="text-muted" style="margin:4px 0 0;"><?= (int) $paginator->total ?> compte(s) au total.</p>
    </div>
    <a href="<?= e(url('/admin/utilisateurs/creer')) ?>" class="btn btn-primary">Ajouter un utilisateur</a>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="get" action="<?= e(url('/admin/utilisateurs')) ?>" class="form-row" style="align-items:end;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="q">Recherche</label>
                <input type="text" id="q" name="q" class="form-control" placeholder="Nom, prenom ou email" value="<?= e($recherche) ?>">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="role">Role</label>
                <select id="role" name="role" class="form-control">
                    <option value="">Tous les roles</option>
                    <?php foreach (['administrateur', 'formateur', 'etudiant'] as $r): ?>
                        <option value="<?= e($r) ?>" <?= $role === $r ? 'selected' : '' ?>><?= e(role_label($r)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <button type="submit" class="btn btn-outline">Filtrer</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Statut</th>
                    <th>Inscrit le</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($utilisateurs === []): ?>
                    <tr><td colspan="6" class="text-soft">Aucun utilisateur ne correspond a ces criteres.</td></tr>
                <?php endif; ?>
                <?php foreach ($utilisateurs as $u): ?>
                    <tr>
                        <td><?= e($u['prenom'] . ' ' . $u['nom']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><span class="badge badge-primaire"><?= e(role_label($u['role'])) ?></span></td>
                        <td>
                            <?php if ($u['statut'] === 'actif'): ?>
                                <span class="badge badge-succes">Actif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Suspendu</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-soft"><?= e(format_date($u['date_creation'])) ?></td>
                        <td class="cell-actions">
                            <a href="<?= e(url('/admin/utilisateurs/' . $u['id'] . '/modifier')) ?>" class="btn btn-outline btn-sm">Modifier</a>
                            <form method="post" action="<?= e(url('/admin/utilisateurs/' . $u['id'] . '/supprimer')) ?>" data-confirm="Supprimer cet utilisateur ?">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../partials/pagination.php'; ?>
