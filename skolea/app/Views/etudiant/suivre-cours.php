<section class="section">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?= e(url('/mes-cours')) ?>">Mes cours</a> / <?= e($inscription['cours_titre']) ?>
        </div>

        <div class="section-head">
            <div>
                <h1 style="margin:0;"><?= e($inscription['cours_titre']) ?></h1>
                <p class="text-muted" style="margin:6px 0 0;">
                    Par <?= e($inscription['formateur_prenom'] . ' ' . $inscription['formateur_nom']) ?> &middot; <?= e($inscription['categorie_nom']) ?>
                </p>
            </div>
            <form method="post" action="<?= e(url('/mes-cours/' . $inscription['cours_id'] . '/desinscrire')) ?>" data-confirm="Vous desinscrire de ce cours ? Votre progression sera perdue.">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline">Se desinscrire</button>
            </form>
        </div>

        <div class="card" style="margin-bottom:24px;">
            <div class="card-body">
                <div class="cluster" style="justify-content:space-between;margin-bottom:8px;">
                    <strong>Progression</strong>
                    <span class="text-soft"><?= (int) $inscription['progression'] ?>%</span>
                </div>
                <div class="progress-bar"><span style="width:<?= (int) $inscription['progression'] ?>%"></span></div>
            </div>
        </div>

        <?php foreach ($modules as $module): ?>
            <?php $estTermine = in_array((int) $module['id'], $modulesTermines, true); ?>
            <div class="module-item">
                <div class="cluster" style="justify-content:space-between;">
                    <div>
                        <strong>#<?= (int) $module['ordre'] ?> &middot; <?= e($module['titre']) ?></strong>
                        <?php if ($module['description']): ?>
                            <p class="text-muted" style="margin:6px 0 0;font-size:.86rem;"><?= e($module['description']) ?></p>
                        <?php endif; ?>
                    </div>
                    <form method="post" action="<?= e(url('/mes-cours/' . $inscription['cours_id'] . '/modules/' . $module['id'] . '/terminer')) ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm <?= $estTermine ? 'btn-outline' : 'btn-primary' ?>">
                            <?= $estTermine ? 'Marque comme termine' : 'Marquer comme termine' ?>
                        </button>
                    </form>
                </div>

                <?php $ressources = $ressourcesParModule[$module['id']] ?? []; ?>
                <?php if ($ressources !== []): ?>
                    <div style="margin-top:12px;">
                        <?php foreach ($ressources as $ressource): ?>
                            <?php $href = str_starts_with((string) $ressource['contenu'], 'http') ? $ressource['contenu'] : uploads_url($ressource['contenu']); ?>
                            <div class="resource-row">
                                <span class="resource-icon"><?= match ($ressource['type']) { 'video' => '&#9654;', 'quiz' => '?', default => '&#128196;' } ?></span>
                                <div class="spacer">
                                    <strong style="font-size:.88rem;"><?= e($ressource['titre']) ?></strong>
                                    <div class="text-soft" style="font-size:.78rem;"><?= e(type_ressource_label($ressource['type'])) ?></div>
                                </div>
                                <a href="<?= e($href) ?>" target="_blank" rel="noopener" class="btn btn-outline btn-sm">Consulter</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
