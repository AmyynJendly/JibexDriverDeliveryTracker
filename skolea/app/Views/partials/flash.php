<?php $__flashes = flash_get(); ?>
<?php foreach ($__flashes as $type => $message): ?>
    <div class="alert alert-<?= e($type) ?>" data-flash>
        <?= e($message) ?>
    </div>
<?php endforeach; ?>
