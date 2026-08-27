<?php
/**
 * @var array<int, array{label: string, value: int}> $items
 * @var int|null $max
 */
$max ??= max(1, ...array_map(static fn ($item) => (int) $item['value'], $items ?: [['value' => 0]]));
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
