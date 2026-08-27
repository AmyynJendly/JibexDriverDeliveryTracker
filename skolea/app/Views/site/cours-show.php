<?php $user = current_user(); ?>
<section class="section" style="padding-bottom:24px;">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?= e(url('/cours')) ?>">Catalogue</a> / <?= e($cours['titre']) ?>
        </div>

        <div class="grid" style="grid-template-columns:1.7fr 1fr;align-items:start;">
            <div>
                <div class="tag-list" style="margin-bottom:14px;">
                    <span class="badge badge-primaire"><?= e($cours['categorie_nom']) ?></span>
                    <span class="badge badge-neutre"><?= e(niveau_label($cours['niveau'])) ?></span>
                </div>
                <h1><?= e($cours['titre']) ?></h1>
                <p class="text-muted"><?= nl2br(e($cours['description'])) ?></p>

                <div class="cluster" style="margin:24px 0;">
                    <span class="user-avatar"><?= e(mb_strtoupper(mb_substr($cours['formateur_prenom'], 0, 1) . mb_substr($cours['formateur_nom'], 0, 1))) ?></span>
                    <div>
                        <strong style="display:block;font-size:.9rem;"><?= e($cours['formateur_prenom'] . ' ' . $cours['formateur_nom']) ?></strong>
                        <span class="text-soft" style="font-size:.8rem;">Formateur</span>
                    </div>
                    <span class="spacer"></span>
                    <span class="text-soft" style="font-size:.85rem;"><?= (int) $cours['nb_inscrits'] ?> etudiant(s) inscrit(s)</span>
                </div>

                <div class="card">
                    <div class="card-header"><h3 style="margin:0;">Contenu du cours</h3></div>
                    <div class="card-body">
                        <?php if ($modules === []): ?>
                            <p class="text-soft">Le contenu de ce cours sera bientot disponible.</p>
                        <?php endif; ?>
                        <?php foreach ($modules as $module): ?>
                            <div class="module-item">
                                <strong>#<?= (int) $module['ordre'] ?> &middot; <?= e($module['titre']) ?></strong>
                                <?php if ($module['description']): ?>
                                    <p class="text-muted" style="margin:6px 0 0;font-size:.86rem;"><?= e($module['description']) ?></p>
                                <?php endif; ?>
                                <p class="text-soft" style="margin:8px 0 0;font-size:.78rem;"><?= (int) $module['nb_ressources'] ?> ressource(s)</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 style="margin-top:0;">Rejoindre ce cours</h3>
                    <p class="text-muted" style="font-size:.9rem;"><?= count($modules) ?> module(s) au programme.</p>

                    <?php if ($inscription): ?>
                        <p class="text-muted" style="font-size:.85rem;">Vous suivez deja ce cours.</p>
                        <a href="<?= e(url('/mes-cours/' . $cours['id'])) ?>" class="btn btn-primary btn-block">Continuer le cours</a>
                    <?php elseif ($user && $user['role'] === 'etudiant'): ?>
                        <form method="post" action="<?= e(url('/cours/' . $cours['id'] . '/inscription')) ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary btn-block">S'inscrire gratuitement</button>
                        </form>
                    <?php elseif ($user): ?>
                        <p class="text-soft" style="font-size:.85rem;">Seuls les comptes etudiant peuvent s'inscrire a un cours.</p>
                    <?php else: ?>
                        <a href="<?= e(url('/connexion')) ?>" class="btn btn-primary btn-block">Se connecter pour s'inscrire</a>
                        <p class="text-soft" style="font-size:.8rem;margin-top:10px;">
                            Pas encore de compte ? <a href="<?= e(url('/inscription')) ?>" style="color:var(--color-primary);font-weight:600;">Inscrivez-vous</a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
