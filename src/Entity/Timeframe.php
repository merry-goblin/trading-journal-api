<?php

namespace App\Entity;

use App\Repository\TimeframeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimeframeRepository::class)]
class Timeframe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private ?string $code = null;

    #[ORM\Column]
    private ?int $seconds = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getSeconds(): ?int
    {
        return $this->seconds;
    }

    public function setSeconds(int $seconds): static
    {
        $this->seconds = $seconds;

        return $this;
    }
}
