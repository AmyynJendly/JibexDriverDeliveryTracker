<?php
/** @var string $content */
$title ??= 'Tableau de bord';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> - Skolea</title>
    <link rel="icon" type="image/svg+xml" href="<?= e(asset('img/favicon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
    <div class="back-shell">
        <?php include __DIR__ . '/../partials/back-sidebar.php'; ?>

        <div class="back-main">
            <?php include __DIR__ . '/../partials/back-topbar.php'; ?>
            <div class="back-content">
                <?php include __DIR__ . '/../partials/flash.php'; ?>
                <?= $content ?>
            </div>
        </div>
    </div>

    <script src="<?= e(asset('js/main.js')) ?>"></script>
</body>
</html>
