<?php $title = 'Acces refuse'; ?>
<div class="container section">
    <div class="empty-state">
        <h1 style="font-size:3.5rem;">403</h1>
        <h3><?= e($message ?? "Vous n'avez pas acces a cette page.") ?></h3>
        <p>Si vous pensez qu'il s'agit d'une erreur, contactez un administrateur.</p>
        <a href="<?= e(url('/')) ?>" class="btn btn-primary">Retour a l'accueil</a>
    </div>
</div>
