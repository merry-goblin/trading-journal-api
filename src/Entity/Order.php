<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Asset $asset = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Timeframe $timeframe = null;

    #[ORM\Column(name: 'created_at')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 15)]
    private ?string $order_type = null;

    #[ORM\Column(length: 5)]
    private ?string $direction = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 5, nullable: true)]
    private ?string $price = null;

    #[ORM\Column(name: 'stop_price', type: Types::DECIMAL, precision: 10, scale: 5, nullable: true)]
    private ?string $stopPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $size = null;

    #[ORM\Column(name: 'stop_loss', type: Types::DECIMAL, precision: 10, scale: 5, nullable: true)]
    private ?string $stopLoss = null;

    #[ORM\Column(name: 'take_profit', type: Types::DECIMAL, precision: 10, scale: 5, nullable: true)]
    private ?string $takeProfit = null;

    #[ORM\Column(length: 15)]
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    public function setAsset(?Asset $asset): static
    {
        $this->asset = $asset;

        return $this;
    }

    public function getTimeframe(): ?Timeframe
    {
        return $this->timeframe;
    }

    public function setTimeframe(?Timeframe $timeframe): static
    {
        $this->timeframe = $timeframe;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getOrderType(): ?string
    {
        return $this->orderType;
    }

    public function setOrderType(string $orderType): static
    {
        $this->orderType = $orderType;

        return $this;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): static
    {
        $this->direction = $direction;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStopPrice(): ?string
    {
        return $this->stopPrice;
    }

    public function setStopPrice(?string $stopPrice): static
    {
        $this->stopPrice = $stopPrice;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getStopLoss(): ?string
    {
        return $this->stopLoss;
    }

    public function setStopLoss(?string $stopLoss): static
    {
        $this->stopLoss = $stopLoss;

        return $this;
    }

    public function getTakeProfit(): ?string
    {
        return $this->takeProfit;
    }

    public function setTakeProfit(?string $takeProfit): static
    {
        $this->takeProfit = $takeProfit;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
