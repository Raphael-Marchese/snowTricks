<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\MessageRepository;

class MessageCollection implements \IteratorAggregate, \Countable
{
    private array $messages;

    public function __construct(array $messages = [], private ?MessageRepository $messageRepository = null)
    {
        $this->messages = $messages ?: $this->loadMessages();
    }

    public function paginate(int $page, int $perPage): self
    {
        return new self(\array_slice($this->messages, ($page - 1) * $perPage, $perPage));
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->messages);
    }

    public function count(): int
    {
        return \count($this->messages);
    }

    private function loadMessages(): array
    {
        if ($this->messageRepository === null) {
            throw new \LogicException('MessageRepository is required to load messagess dynamically.');
        }

        return $this->messageRepository->findAll();
    }
}
