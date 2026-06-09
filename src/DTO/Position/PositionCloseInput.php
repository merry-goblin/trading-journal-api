<?php
namespace App\DTO\Position;
use Symfony\Component\Validator\Constraints as Assert;

class PositionCloseInput
{
    #[Assert\NotBlank]
    public string $closedAt;

    #[Assert\NotBlank]
    public string $exitPrice;

    public ?string $pnl        = null;
    public ?string $pnlPercent = null;
    public ?string $rr         = null;
}
