<?php

namespace App\Entity;

use App\Repository\PlanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanRepository::class)]
class Plan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeId = null;

    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paymentLink = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getStripeId(): ?string
    {
        return $this->stripeId;
    }

    public function setStripeId(?string $stripeId): static
    {
        $this->stripeId = $stripeId;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    public function getPaymentLink(): ?string
    {
        return $this->paymentLink;
    }

    public function setPaymentLink(?string $paymentLink): static
    {
        $this->paymentLink = $paymentLink;

        return $this;
    }

    public function __toString(): string
    {
        // Return a human readable representation so Doctrine proxies
        // and forms can safely cast Plan objects to string.
        // Prefer name, then slug, then id as fallback.
        if (!empty($this->name)) {
            return (string) $this->name;
        }

        if (!empty($this->slug)) {
            return (string) $this->slug;
        }

        return $this->id !== null ? (string) $this->id : '';
    }
}
