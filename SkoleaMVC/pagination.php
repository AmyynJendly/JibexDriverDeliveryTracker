<?php
// Partiel de pagination. La page qui l'inclut doit avoir defini
// $page (page courante) et $totalPages (nombre total de pages) avant.
if ($totalPages > 1):
?>
    <nav class="pagination" aria-label="Pagination">
        <?php
        $parametres = $_GET;
        $parametres['page'] = $page - 1;
        ?>
        <a href="?<?= http_build_query($parametres) ?>" class="<?= $page > 1 ? '' : 'is-disabled' ?>">&laquo;</a>

        <?php for ($i = 1; $i <= $totalPages; $i++): $parametres['page'] = $i; ?>
            <a href="?<?= http_build_query($parametres) ?>" class="<?= $i === $page ? 'is-active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php $parametres['page'] = $page + 1; ?>
        <a href="?<?= http_build_query($parametres) ?>" class="<?= $page < $totalPages ? '' : 'is-disabled' ?>">&raquo;</a>
    </nav>
<?php endif; ?>
