<?php $title = 'Page introuvable'; ?>
<div class="container section">
    <div class="empty-state">
        <h1 style="font-size:3.5rem;">404</h1>
        <h3>Cette page n'existe pas ou plus.</h3>
        <p>Verifiez l'adresse saisie ou retournez a l'accueil.</p>
        <a href="<?= e(url('/')) ?>" class="btn btn-primary">Retour a l'accueil</a>
    </div>
</div>
