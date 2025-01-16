<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Service\FigureCollection;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('TrickGrid', template: 'components/TrickGrid.html.twig')]
class TrickGrid
{

    use ComponentToolsTrait;
    use DefaultActionTrait;

    private const PER_PAGE = 5;

    #[LiveProp]
    public int $page = 1;

    public function __construct(private readonly FigureCollection $figures)
    {
    }

    #[LiveAction]
    public function more(): void
    {
        ++$this->page;
    }

    public function getFigureNumber(): int
    {
        return $this->page * self::PER_PAGE;
    }

    public function hasMore(): bool
    {
        return \count($this->figures) > ($this->page * self::PER_PAGE);
    }

    public function getItems(): array
    {
        $figures = $this->figures->paginate($this->page, self::PER_PAGE);

        $items = [];
        foreach ($figures as $i => $figure) {
            $items[] = [
                'id' => $figure->id,
                'name' => $figure->name,
                'illustrations' => $figure->illustrations,
            ];
        }

        return $items;
    }

}
