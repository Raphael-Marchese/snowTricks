<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Service\MessageCollection;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('MessageGrid', template: 'components/MessageGrid.html.twig')]
class MessageGrid
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    private const PER_PAGE = 5;

    #[LiveProp]
    public int $page = 1;

    public function __construct(private readonly MessageCollection $messages)
    {
    }

    #[LiveAction]
    public function more(): void
    {
        ++$this->page;
    }

    public function getMessageNumber(): int
    {
        return $this->page * self::PER_PAGE;
    }

    public function hasMore(): bool
    {
        return \count($this->messages) > ($this->page * self::PER_PAGE);
    }

    public function getItems(): array
    {
        $messages = $this->messages->paginate($this->page, self::PER_PAGE);

        $items = [];
        foreach ($messages as $i => $message) {
            $items[] = [
                'id' => $message->id,
                'content' => $message->content,
                'author' => $message->author,
                'createdAt' => $message->createdAt,
                'updatedAt' => $message->updatedAt,
            ];
        }

        return $items;
    }
}