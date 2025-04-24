<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Repository\ImageRepository;
use App\Repository\VideoRepository;
use App\Service\FigureCollection;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

use function count;

#[AsLiveComponent('TrickGrid', template: 'components/TrickGrid.html.twig')]
class TrickGrid
{

    use ComponentToolsTrait;
    use DefaultActionTrait;

    private const PER_PAGE = 5;

    #[LiveProp]
    public int $page = 1;

    public function __construct(
        private readonly FigureCollection $figures,
        private readonly ImageRepository $imageRepository,
        private readonly VideoRepository $videoRepository
    ) {
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
        return count($this->figures) > ($this->page * self::PER_PAGE);
    }

    public function getItems(): array
    {
        $figures = $this->figures->paginate($this->page, self::PER_PAGE);

        $items = [];
        foreach ($figures as $figure) {
            $images = $this->imageRepository->findBy(['figure' => $figure->id]);
            $videos = $this->videoRepository->findBy(['figure' => $figure->id]);
            $items[] = [
                'id' => $figure->id,
                'name' => $figure->name,
                'illustrations' => $images,
                'videos' => $videos,
            ];
        }

        return $items;
    }

}
