<div class="breadcrumb">
    <a href="<?= e(url('/formateur/cours')) ?>">Mes cours</a> / <?= e($cours['titre']) ?>
</div>

<div class="section-head">
    <div>
        <h2 style="margin:0;"><?= e($cours['titre']) ?></h2>
        <div class="cluster" style="margin-top:8px;">
            <?php if ($cours['statut'] === 'publie'): ?>
                <span class="badge badge-succes">Publie</span>
            <?php else: ?>
                <span class="badge badge-attente">Brouillon</span>
            <?php endif; ?>
            <span class="badge badge-neutre"><?= e($cours['categorie_nom']) ?></span>
            <span class="badge badge-neutre"><?= e(niveau_label($cours['niveau'])) ?></span>
        </div>
    </div>
    <div class="cluster">
        <a href="<?= e(url('/formateur/cours/' . $cours['id'] . '/modifier')) ?>" class="btn btn-outline">Modifier le cours</a>
        <form method="post" action="<?= e(url('/formateur/cours/' . $cours['id'] . '/supprimer')) ?>" data-confirm="Supprimer definitivement ce cours et tout son contenu ?">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-danger">Supprimer</button>
        </form>
    </div>
</div>

<div class="stat-grid" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card"><span class="stat-value"><?= count($modules) ?></span><span class="stat-label">Modules</span></div>
    <div class="stat-card"><span class="stat-value"><?= count($participants) ?></span><span class="stat-label">Etudiants inscrits</span></div>
    <div class="stat-card"><span class="stat-value"><?= (int) array_sum(array_column($modules, 'nb_ressources')) ?></span><span class="stat-label">Ressources</span></div>
</div>

<div class="grid" style="grid-template-columns:1.6fr 1fr;align-items:start;">
    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;">Modules</h3>
            <a href="<?= e(url('/formateur/cours/' . $cours['id'] . '/modules/creer')) ?>" class="btn btn-primary btn-sm">Ajouter un module</a>
        </div>
        <div class="card-body">
            <?php if ($modules === []): ?>
                <p class="text-soft">Aucun module pour le moment. Ajoutez-en un pour commencer a structurer ce cours.</p>
            <?php endif; ?>
            <?php foreach ($modules as $module): ?>
                <div class="module-item">
                    <div class="cluster" style="justify-content:space-between;">
                        <div>
                            <strong>#<?= (int) $module['ordre'] ?> &middot; <?= e($module['titre']) ?></strong>
                            <?php if ($module['description']): ?>
                                <p class="text-muted" style="margin:6px 0 0;font-size:.86rem;"><?= e($module['description']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="cluster">
                            <a href="<?= e(url('/formateur/modules/' . $module['id'] . '/modifier')) ?>" class="btn btn-outline btn-sm">Modifier</a>
                            <form method="post" action="<?= e(url('/formateur/modules/' . $module['id'] . '/supprimer')) ?>" data-confirm="Supprimer ce module et ses ressources ?">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </div>
                    </div>

                    <?php $ressources = $ressourcesParModule[$module['id']] ?? []; ?>
                    <div style="margin-top:12px;">
                        <?php foreach ($ressources as $ressource): ?>
                            <div class="resource-row">
                                <span class="resource-icon"><?= match ($ressource['type']) { 'video' => '&#9654;', 'quiz' => '?', default => '&#128196;' } ?></span>
                                <div class="spacer">
                                    <strong style="font-size:.88rem;"><?= e($ressource['titre']) ?></strong>
                                    <div class="text-soft" style="font-size:.78rem;"><?= e(type_ressource_label($ressource['type'])) ?></div>
                                </div>
                                <a href="<?= e(url('/formateur/ressources/' . $ressource['id'] . '/modifier')) ?>" class="btn btn-outline btn-sm">Modifier</a>
                                <form method="post" action="<?= e(url('/formateur/ressources/' . $ressource['id'] . '/supprimer')) ?>" data-confirm="Supprimer cette ressource ?">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= e(url('/formateur/modules/' . $module['id'] . '/ressources/creer')) ?>" class="btn btn-ghost btn-sm" style="margin-top:8px;">+ Ajouter une ressource</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Participants</h3></div>
        <div class="card-body">
            <?php if ($participants === []): ?>
                <p class="text-soft">Aucun etudiant inscrit pour le moment.</p>
            <?php endif; ?>
            <?php foreach ($participants as $p): ?>
                <div class="resource-row">
                    <span class="user-avatar"><?= e(mb_strtoupper(mb_substr($p['prenom'], 0, 1) . mb_substr($p['nom'], 0, 1))) ?></span>
                    <div class="spacer">
                        <strong style="font-size:.86rem;display:block;"><?= e($p['prenom'] . ' ' . $p['nom']) ?></strong>
                        <span class="text-soft" style="font-size:.76rem;"><?= (int) $p['progression'] ?>% termine</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
