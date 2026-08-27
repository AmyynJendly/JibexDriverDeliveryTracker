<div class="stat-grid">
    <div class="stat-card">
        <span class="stat-value"><?= (int) $nbUtilisateurs ?></span>
        <span class="stat-label">Utilisateurs</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= (int) $nbFormateurs ?></span>
        <span class="stat-label">Formateurs</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= (int) $nbEtudiants ?></span>
        <span class="stat-label">Etudiants</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= (int) $nbCoursPublies ?></span>
        <span class="stat-label">Cours publies</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= (int) $nbCoursBrouillon ?></span>
        <span class="stat-label">Cours en brouillon</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= (int) $nbInscriptions ?></span>
        <span class="stat-label">Inscriptions</span>
    </div>
</div>

<div class="grid grid-cols-2">
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Cours par categorie</h3></div>
        <div class="card-body">
            <?php $items = $repartitionCategorie; $max = null; include __DIR__ . '/../partials/bar-list.php'; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Utilisateurs par role</h3></div>
        <div class="card-body">
            <?php $items = $repartitionRole; $max = null; include __DIR__ . '/../partials/bar-list.php'; ?>
        </div>
    </div>
</div>

<div class="card" style="margin-top:24px;">
    <div class="card-header">
        <h3 style="margin:0;">Derniers utilisateurs inscrits</h3>
        <a href="<?= e(url('/admin/utilisateurs')) ?>" class="btn btn-outline btn-sm">Voir tous</a>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Utilisateur</th><th>Email</th><th>Role</th><th>Inscrit le</th></tr></thead>
            <tbody>
                <?php foreach ($derniersUtilisateurs as $u): ?>
                    <tr>
                        <td><?= e($u['prenom'] . ' ' . $u['nom']) ?></td>
                        <td class="text-muted"><?= e($u['email']) ?></td>
                        <td><span class="badge badge-primaire"><?= e(role_label($u['role'])) ?></span></td>
                        <td class="text-soft"><?= e(format_date($u['date_creation'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
