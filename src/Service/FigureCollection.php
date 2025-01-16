<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\FigureRepository;

class FigureCollection implements \IteratorAggregate, \Countable
{
    private array $figures;

    public function __construct(array $figures = [], private ?FigureRepository $figureRepository = null)
    {
        $this->figures = $figures ?: $this->loadFigures();
    }

    public function paginate(int $page, int $perPage): self
    {
        return new self(\array_slice($this->figures, ($page - 1) * $perPage, $perPage));
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->figures);
    }

    public function count(): int
    {
        return \count($this->figures);
    }

    private function loadFigures(): array
    {
        if ($this->figureRepository === null) {
            throw new \LogicException('FigureRepository is required to load figures dynamically.');
        }

        return $this->figureRepository->findAll();
    }

}
