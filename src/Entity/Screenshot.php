<?php

namespace App\Entity;

use App\Repository\ScreenshotRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScreenshotRepository::class)]
class Screenshot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filePath = null;

    #[ORM\Column(name: 'created_at')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Asset $asset = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Timeframe $timeframe = null;

    #[ORM\ManyToOne(inversedBy: 'screenshots')]
    private ?ChartObservation $observation = null;

    #[ORM\ManyToOne(inversedBy: 'screenshots')]
    private ?Position $position = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTime $periodStart = null;

    #[ORM\Column]
    private ?\DateTime $periodEnd = null;

    #[ORM\Column(length: 10)]
    private ?string $source = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): static
    {
        $this->filePath = $filePath;

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

    public function getObservation(): ?ChartObservation
    {
        return $this->observation;
    }

    public function setObservation(?ChartObservation $observation): static
    {
        $this->observation = $observation;

        return $this;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPeriodStart(): ?\DateTime
    {
        return $this->periodStart;
    }

    public function setPeriodStart(\DateTime $periodStart): static
    {
        $this->periodStart = $periodStart;

        return $this;
    }

    public function getPeriodEnd(): ?\DateTime
    {
        return $this->periodEnd;
    }

    public function setPeriodEnd(\DateTime $periodEnd): static
    {
        $this->periodEnd = $periodEnd;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;

        return $this;
    }
}
