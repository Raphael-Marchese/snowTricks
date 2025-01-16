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
        $colors = $this->getColors();

        $items = [];
        foreach ($figures as $i => $figure) {
            $items[] = [
                'id' => $id = ($this->page - 1) * self::PER_PAGE + $i,
                'name' => $figure->name,
                'illustrations' => $figure->illustrations,
                'color' => $colors[$id % \count($colors)],
            ];
        }

        return $items;
    }

    public function getColors(): array
    {
        return [
            '#fbf8cc',
            '#fde4cf',
            '#ffcfd2',
            '#f1c0e8',
            '#cfbaf0',
            '#a3c4f3',
            '#90dbf4',
            '#8eecf5',
            '#98f5e1',
            '#b9fbc0',
            '#b9fbc0',
            '#ffc9c9',
            '#d7ffc9',
            '#c9fffb',
        ];
    }
}