<div class="grid grid-cols-2">
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Cours par statut</h3></div>
        <div class="card-body">
            <?php $items = $coursParStatut; $max = null; include __DIR__ . '/../partials/bar-list.php'; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Inscriptions par statut</h3></div>
        <div class="card-body">
            <?php $items = $inscriptionsParStatut; $max = null; include __DIR__ . '/../partials/bar-list.php'; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Cours publies par categorie</h3></div>
        <div class="card-body">
            <?php $items = $repartitionCategorie; $max = null; include __DIR__ . '/../partials/bar-list.php'; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Nouveaux comptes (6 derniers mois)</h3></div>
        <div class="card-body">
            <?php $items = $inscriptionsParMois; $max = null; include __DIR__ . '/../partials/bar-list.php'; ?>
        </div>
    </div>
</div>
