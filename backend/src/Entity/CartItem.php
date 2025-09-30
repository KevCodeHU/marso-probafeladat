<?php

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cart $cart = null;

    #[ORM\Column(type: 'integer')]
    private int $productId;

    #[ORM\Column(type: 'integer')]
    private int $quantity = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }
    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;
        return $this;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }
    public function setProductId(int $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function fetchProduct(EntityManagerInterface $em): ?Product
    {
        return $em->getRepository(Product::class)->find($this->productId);
    }
}
