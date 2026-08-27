<?php if ($paginator->totalPages > 1): ?>
    <nav class="pagination" aria-label="Pagination">
        <a href="<?= e(query_with(['page' => $paginator->page - 1])) ?>"
           class="<?= $paginator->hasPrevious() ? '' : 'is-disabled' ?>" aria-label="Page precedente">&laquo;</a>

        <?php for ($i = 1; $i <= $paginator->totalPages; $i++): ?>
            <a href="<?= e(query_with(['page' => $i])) ?>"
               class="<?= $i === $paginator->page ? 'is-active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <a href="<?= e(query_with(['page' => $paginator->page + 1])) ?>"
           class="<?= $paginator->hasNext() ? '' : 'is-disabled' ?>" aria-label="Page suivante">&raquo;</a>
    </nav>
<?php endif; ?>
