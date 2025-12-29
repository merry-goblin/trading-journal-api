<?php

namespace App\Entity;

use App\Repository\ChartObservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChartObservationRepository::class)]
class ChartObservation
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

    #[ORM\Column(name: 'observed_at')]
    private ?\DateTimeImmutable $observedAt = null;

    #[ORM\Column(length: 25)]
    private ?string $trend = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    /**
     * @var Collection<int, Screenshot>
     */
    #[ORM\OneToMany(targetEntity: Screenshot::class, mappedBy: 'observation')]
    private Collection $screenshots;

    /**
     * @var Collection<int, Position>
     */
    #[ORM\ManyToMany(targetEntity: Position::class, mappedBy: 'observations')]
    private Collection $positions;

    public function __construct()
    {
        $this->screenshots = new ArrayCollection();
        $this->positions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getObservedAt(): ?\DateTime
    {
        return $this->observedAt;
    }

    public function setObservedAt(\DateTime $observedAt): static
    {
        $this->observedAt = $observedAt;

        return $this;
    }

    public function getTrend(): ?string
    {
        return $this->trend;
    }

    public function setTrend(string $trend): static
    {
        $this->trend = $trend;

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
            $screenshot->setObservation($this);
        }

        return $this;
    }

    public function removeScreenshot(Screenshot $screenshot): static
    {
        if ($this->screenshots->removeElement($screenshot)) {
            // set the owning side to null (unless already changed)
            if ($screenshot->getObservation() === $this) {
                $screenshot->setObservation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Position>
     */
    public function getPositions(): Collection
    {
        return $this->positions;
    }

    public function addPosition(Position $position): static
    {
        if (!$this->positions->contains($position)) {
            $this->positions->add($position);
            $position->addObservation($this);
        }

        return $this;
    }

    public function removePosition(Position $position): static
    {
        if ($this->positions->removeElement($position)) {
            $position->removeObservation($this);
        }

        return $this;
    }
}
