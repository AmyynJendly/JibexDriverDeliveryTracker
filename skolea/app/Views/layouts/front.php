<?php
$title ??= 'Skolea';
$pageTitle = $title === 'Skolea' ? 'Skolea - Plateforme e-learning' : $title . ' - Skolea';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="Skolea, la plateforme e-learning pour creer, suivre et rejoindre des cours en ligne.">
    <link rel="icon" type="image/svg+xml" href="<?= e(asset('img/favicon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
    <?php include __DIR__ . '/../partials/front-nav.php'; ?>

    <main>
        <?php $flashListe = flash_get(); ?>
        <?php if ($flashListe !== []): ?>
            <div class="container" style="padding-top:20px;">
                <?php foreach ($flashListe as $flashType => $flashMessage): ?>
                    <div class="alert alert-<?= e($flashType) ?>" data-flash><?= e($flashMessage) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?= $content ?>
    </main>

    <?php include __DIR__ . '/../partials/front-footer.php'; ?>

    <script src="<?= e(asset('js/main.js')) ?>"></script>
    <script src="<?= e(asset('js/validation.js')) ?>"></script>
</body>
</html>
