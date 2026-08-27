<?php
// Affiche une liste de barres horizontales. La page qui inclut ce fichier
// doit avoir defini $items (liste de ['label' => ..., 'value' => ...]).
$max = 1;
foreach ($items as $item) {
    if ((int) $item['value'] > $max) {
        $max = (int) $item['value'];
    }
}
?>
<div class="bar-list">
    <?php if ($items === []): ?>
        <p class="text-soft" style="font-size:.85rem;">Aucune donnee pour le moment.</p>
    <?php endif; ?>
    <?php foreach ($items as $item): ?>
        <div class="bar-row">
            <span class="text-muted"><?= e($item['label']) ?></span>
            <div class="bar-track">
                <div class="bar-fill" style="width: <?= (int) round(($item['value'] / $max) * 100) ?>%"></div>
            </div>
            <strong><?= (int) $item['value'] ?></strong>
        </div>
    <?php endforeach; ?>
</div>
