<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\FoodRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['foodList', 'foodByCategory'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['foodList', 'foodByCategory'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['foodList', 'foodByCategory'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['foodList', 'foodByCategory'])]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['foodList', 'foodByCategory'])]
    private ?string $image = null;

    #[ORM\Column]
    #[Groups(['foodList', 'foodByCategory'])]
    private ?bool $featured = null;

    #[ORM\Column]
    #[Groups(['foodList', 'foodByCategory'])]
    private ?bool $active = null;

    #[ORM\ManyToOne(inversedBy: 'foods')]
    #[Groups(['foodList'])]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): static
    {
        $this->featured = $featured;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
