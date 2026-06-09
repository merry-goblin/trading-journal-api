<?php

namespace App\Entity;

use App\Repository\ChartObservation\ChartObservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Une observation de marche liee optionnellement a un ordre ou une position.
 *
 * Contextes possibles :
 *  - order_id renseigne    → observation pre-trade ou pendant l'ordre
 *  - position_id renseigne → observation pendant ou apres une position
 *  - aucun FK renseigne    → analyse libre (pre-trade sans ordre pose)
 *
 * Un screenshot associe porte obligatoirement observation_id NOT NULL.
 */
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

    /**
     * Sentiment de marche : bull | bear | neutral
     * Nullable : une observation peut etre creee automatiquement (capture de cloture)
     * avant que l'utilisateur renseigne son sentiment depuis Vue.js.
     */
    #[ORM\Column(length: 25, nullable: true)]
    private ?string $trend = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    /**
     * Lien optionnel vers l'ordre associe a cette observation.
     * Renseigne quand l'observation est faite avant ou pendant un ordre en attente.
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Order $order = null;

    /**
     * Lien optionnel vers la position associee a cette observation.
     * Renseigne quand l'observation est faite pendant ou apres une position.
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Position $position = null;

    /**
     * @var Collection<int, Screenshot>
     */
    #[ORM\OneToMany(targetEntity: Screenshot::class, mappedBy: 'observation')]
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
    public function setTimeframe(?Timeframe $timeframe): static { $this->timeframe = $timeframe; return $this; }

    public function getObservedAt(): ?\DateTimeImmutable { return $this->observedAt; }
    public function setObservedAt(\DateTimeImmutable $observedAt): static
    {
        $this->observedAt = $observedAt;
        return $this;
    }

    public function getTrend(): ?string { return $this->trend; }
    public function setTrend(?string $trend): static { $this->trend = $trend; return $this; }

    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $comment): static { $this->comment = $comment; return $this; }

    public function getOrder(): ?Order { return $this->order; }
    public function setOrder(?Order $order): static { $this->order = $order; return $this; }

    public function getPosition(): ?Position { return $this->position; }
    public function setPosition(?Position $position): static { $this->position = $position; return $this; }

    /** @return Collection<int, Screenshot> */
    public function getScreenshots(): Collection { return $this->screenshots; }

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
            if ($screenshot->getObservation() === $this) {
                $screenshot->setObservation(null);
            }
        }
        return $this;
    }
}
