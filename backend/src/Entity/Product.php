<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $externalId;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'float')]
    private $price;

    #[ORM\Column(type: 'float', nullable: true)]
    private $netPrice;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    private $category;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $type;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $diameter;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;
        return $this;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function getPrice(): ?float
    {
        return $this->price;
    }
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }
    public function getNetPrice(): ?float
    {
        return $this->netPrice;
    }
    public function setNetPrice(?float $netPrice): self
    {
        $this->netPrice = $netPrice;
        return $this;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }
    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
    public function getCategory(): ?Category
    {
        return $this->category;
    }
    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }
    public function getType(): ?string
    {
        return $this->type;
    }
    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }
    public function getDiameter(): ?int
    {
        return $this->diameter;
    }
    public function setDiameter(?int $diameter): self
    {
        $this->diameter = $diameter;
        return $this;
    }
}
