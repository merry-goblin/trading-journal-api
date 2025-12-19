<?php

namespace App\Entity;

use App\Repository\PositionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PositionRepository::class)]
class Position
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

    #[ORM\Column(name: 'opened_at')]
    private ?\DateTimeImmutable $openedAt = null;

    #[ORM\Column(name: 'closed_at', nullable: true)]
    private ?\DateTimeImmutable $closedAt = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $direction = null;

    #[ORM\Column(name: 'entry_price', type: Types::DECIMAL, precision: 10, scale: 5)]
    private ?string $entryPrice = null;

    #[ORM\Column(name: 'exit_price', type: Types::DECIMAL, precision: 10, scale: 5, nullable: true)]
    private ?string $exitPrice = null;

    #[ORM\Column(name: 'stop_loss', type: Types::DECIMAL, precision: 10, scale: 5, nullable: true)]
    private ?string $stopLoss = null;

    #[ORM\Column(name: 'take_profit', type: Types::DECIMAL, precision: 10, scale: 5, nullable: true)]
    private ?string $takeProfit = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $volume = null;

    #[ORM\Column(name: 'risk_amount', type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $riskAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $pnl = null;

    #[ORM\Column(name: 'pnl_percent', type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $pnlPercent = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $rr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    /**
     * @var Collection<int, Screenshot>
     */
    #[ORM\OneToMany(targetEntity: Screenshot::class, mappedBy: 'position')]
    private Collection $screenshots;

    /**
     * @var Collection<int, ChartObservation>
     */
    #[ORM\ManyToMany(targetEntity: ChartObservation::class, inversedBy: 'positions')]
    private Collection $observations;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class)]
    private Collection $tags;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Order $originOrder = null;

    public function __construct()
    {
        $this->screenshots = new ArrayCollection();
        $this->observations = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

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

    public function getOpenedAt(): ?\DateTimeImmutable
    {
        return $this->openedAt;
    }

    public function setOpenedAt(\DateTimeImmutable $openedAt): static
    {
        $this->openedAt = $openedAt;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(?string $direction): static
    {
        $this->direction = $direction;

        return $this;
    }

    public function getEntryPrice(): ?string
    {
        return $this->entryPrice;
    }

    public function setEntryPrice(string $entryPrice): static
    {
        $this->entryPrice = $entryPrice;

        return $this;
    }

    public function getExitPrice(): ?string
    {
        return $this->exitPrice;
    }

    public function setExitPrice(?string $exitPrice): static
    {
        $this->exitPrice = $exitPrice;

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

    public function getVolume(): ?string
    {
        return $this->volume;
    }

    public function setVolume(string $volume): static
    {
        $this->volume = $volume;

        return $this;
    }

    public function getRiskAmount(): ?string
    {
        return $this->riskAmount;
    }

    public function setRiskAmount(?string $riskAmount): static
    {
        $this->riskAmount = $riskAmount;

        return $this;
    }

    public function getPnl(): ?string
    {
        return $this->pnl;
    }

    public function setPnl(?string $pnl): static
    {
        $this->pnl = $pnl;

        return $this;
    }

    public function getPnlPercent(): ?string
    {
        return $this->pnlPercent;
    }

    public function setPnlPercent(?string $pnlPercent): static
    {
        $this->pnlPercent = $pnlPercent;

        return $this;
    }

    public function getRr(): ?string
    {
        return $this->rr;
    }

    public function setRr(?string $rr): static
    {
        $this->rr = $rr;

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

    /**
     * @return Collection<int, Screenshot>
     */
    public function getScreenshots(): Collection
    {
        return $this->screenshots;
    }

    public function addScreenshot(Screenshot $screenshot): static
    {
        if (!$this->screenshots->contains($screenshot)) {
            $this->screenshots->add($screenshot);
            $screenshot->setPosition($this);
        }

        return $this;
    }

    public function removeScreenshot(Screenshot $screenshot): static
    {
        if ($this->screenshots->removeElement($screenshot)) {
            // set the owning side to null (unless already changed)
            if ($screenshot->getPosition() === $this) {
                $screenshot->setPosition(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChartObservation>
     */
    public function getObservations(): Collection
    {
        return $this->observations;
    }

    public function addObservation(ChartObservation $observation): static
    {
        if (!$this->observations->contains($observation)) {
            $this->observations->add($observation);
        }

        return $this;
    }

    public function removeObservation(ChartObservation $observation): static
    {
        $this->observations->removeElement($observation);

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getOriginOrder(): ?Order
    {
        return $this->originOrder;
    }

    public function setOriginOrder(?Order $originOrder): static
    {
        $this->originOrder = $originOrder;

        return $this;
    }
}
