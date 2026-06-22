<?php

namespace App\Entity;

use App\Repository\ChartObservation\ChartObservationRepository;
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

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $trend = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Order $order = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Position $position = null;

    /**
     * cascade: ['remove'] -> suppression des Screenshots en base quand l'observation est supprimee.
     * Les fichiers physiques sont supprimes par ChartObservationService::delete().
     *
     * @var Collection<int, Screenshot>
     */
    #[ORM\OneToMany(targetEntity: Screenshot::class, mappedBy: 'observation', cascade: ['remove'])]
    private Collection $screenshots;

    public function __construct()
    {
        $this->screenshots = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): static { $this->id = $id; return $this; }

    public function getAsset(): ?Asset { return $this->asset; }
    public function setAsset(?Asset $asset): static { $this->asset = $asset; return $this; }

    public function getTimeframe(): ?Timeframe { return $this->timeframe; }
    public function setTimeframe(?Timeframe $tf): static { $this->timeframe = $tf; return $this; }

    public function getObservedAt(): ?\DateTimeImmutable { return $this->observedAt; }
    public function setObservedAt(\DateTimeImmutable $v): static { $this->observedAt = $v; return $this; }

    public function getTrend(): ?string { return $this->trend; }
    public function setTrend(?string $v): static { $this->trend = $v; return $this; }

    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $v): static { $this->comment = $v; return $this; }

    public function getOrder(): ?Order { return $this->order; }
    public function setOrder(?Order $v): static { $this->order = $v; return $this; }

    public function getPosition(): ?Position { return $this->position; }
    public function setPosition(?Position $v): static { $this->position = $v; return $this; }

    /** @return Collection<int, Screenshot> */
    public function getScreenshots(): Collection { return $this->screenshots; }

    public function addScreenshot(Screenshot $s): static
    {
        if (!$this->screenshots->contains($s)) {
            $this->screenshots->add($s);
            $s->setObservation($this);
        }
        return $this;
    }

    public function removeScreenshot(Screenshot $s): static
    {
        if ($this->screenshots->removeElement($s) && $s->getObservation() === $this)
            $s->setObservation(null);
        return $this;
    }
}
