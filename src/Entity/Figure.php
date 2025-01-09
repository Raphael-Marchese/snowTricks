<?php

namespace App\Entity;

use App\Repository\FigureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FigureRepository::class)]
class Figure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $name = null;

    #[ORM\Column]
    public ?\DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    public ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $figureGroup = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    public ?array $illustrations = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    public ?array $videos = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    public ?User $author = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
