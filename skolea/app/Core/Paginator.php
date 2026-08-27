<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Calcule les informations de pagination (offset, nombre de pages, ...)
 * utilisees a la fois par les modeles (LIMIT/OFFSET) et par les vues
 * (affichage des liens de pagination).
 */
final class Paginator
{
    public readonly int $page;
    public readonly int $parPage;
    public readonly int $total;
    public readonly int $totalPages;
    public readonly int $offset;

    public function __construct(int $pageDemandee, int $parPage, int $total)
    {
        $this->parPage = max(1, $parPage);
        $this->total = max(0, $total);
        $this->totalPages = max(1, (int) ceil($this->total / $this->parPage));
        $this->page = min(max(1, $pageDemandee), $this->totalPages);
        $this->offset = ($this->page - 1) * $this->parPage;
    }

    public static function fromRequest(int $total, int $parPage, string $queryKey = 'page'): self
    {
        $page = (int) ($_GET[$queryKey] ?? 1);

        return new self($page, $parPage, $total);
    }

    public function hasPrevious(): bool
    {
        return $this->page > 1;
    }

    public function hasNext(): bool
    {
        return $this->page < $this->totalPages;
    }
}
