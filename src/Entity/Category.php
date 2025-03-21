<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Service\CategoryNameEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: Types::STRING, unique: true, enumType: CategoryNameEnum::class)]
    public CategoryNameEnum $name;

    public function getName(): string
    {
        return $this->name->value;
    }

    public function setName(CategoryNameEnum $name): self
    {
        $this->name = $name;
        return $this;
    }
}
