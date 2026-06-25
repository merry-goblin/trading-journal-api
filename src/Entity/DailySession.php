<?php

namespace App\Entity;

use App\Repository\DailySession\DailySessionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DailySessionRepository::class)]
#[ORM\Table(name: 'daily_session')]
class DailySession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, unique: true)]
    private DateTimeImmutable $date;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Asset $asset = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $preBias = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $preKeyLevels = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $preAnalysis = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $intraNotes = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $postReview = null;

    #[ORM\Column(name: 'post_emotion_score', nullable: true)]
    private ?int $postEmotionScore = null;

    #[ORM\Column(name: 'post_discipline', nullable: true)]
    private ?bool $postDiscipline = null;

    #[ORM\Column(name: 'created_at')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at')]
    private DateTimeImmutable $updatedAt;

    public function __construct(DateTimeImmutable $date)
    {
        $this->date      = $date;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function touch(): void { $this->updatedAt = new DateTimeImmutable(); }

    public function getId(): ?int                 { return $this->id; }
    public function getDate(): DateTimeImmutable  { return $this->date; }
    public function getAsset(): ?Asset            { return $this->asset; }
    public function setAsset(?Asset $a): static   { $this->asset = $a; return $this; }
    public function getPreBias(): ?string          { return $this->preBias; }
    public function setPreBias(?string $v): static { $this->preBias = $v; return $this; }
    public function getPreKeyLevels(): ?string          { return $this->preKeyLevels; }
    public function setPreKeyLevels(?string $v): static { $this->preKeyLevels = $v; return $this; }
    public function getPreAnalysis(): ?string          { return $this->preAnalysis; }
    public function setPreAnalysis(?string $v): static { $this->preAnalysis = $v; return $this; }
    public function getIntraNotes(): ?string          { return $this->intraNotes; }
    public function setIntraNotes(?string $v): static { $this->intraNotes = $v; return $this; }
    public function getPostReview(): ?string          { return $this->postReview; }
    public function setPostReview(?string $v): static { $this->postReview = $v; return $this; }
    public function getPostEmotionScore(): ?int          { return $this->postEmotionScore; }
    public function setPostEmotionScore(?int $v): static { $this->postEmotionScore = $v; return $this; }
    public function getPostDiscipline(): ?bool          { return $this->postDiscipline; }
    public function setPostDiscipline(?bool $v): static { $this->postDiscipline = $v; return $this; }
    public function getCreatedAt(): DateTimeImmutable   { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable   { return $this->updatedAt; }
}
