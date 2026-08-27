<?php

declare(strict_types=1);

namespace App\Core;

// Calcule la page courante, l'offset et le nombre total de pages
// pour les listes (cours, utilisateurs...).
final class Paginator
{
    public int $page;
    public int $parPage;
    public int $total;
    public int $totalPages;
    public int $offset;

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
