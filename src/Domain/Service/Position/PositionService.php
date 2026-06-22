<?php

namespace App\Domain\Service\Position;

use App\Domain\Exception\NotFoundException\AssetNotFoundException;
use App\Domain\Exception\NotFoundException\OrderNotFoundException;
use App\Domain\Exception\NotFoundException\PositionNotFoundException;
use App\Domain\Exception\NotFoundException\TimeframeNotFoundException;
use App\Domain\Exception\ValidationException\PositionValidationException;
use App\DTO\Position\PositionCloseInput;
use App\DTO\Position\PositionInput;
use App\Entity\Position;
use App\Repository\Asset\AssetRepositoryInterface;
use App\Repository\Order\OrderRepositoryInterface;
use App\Repository\Position\PositionRepositoryInterface;
use App\Repository\Timeframe\TimeframeRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PositionService implements PositionServiceInterface
{
    public function __construct(
        private PositionRepositoryInterface  $repository,
        private AssetRepositoryInterface     $assetRepository,
        private TimeframeRepositoryInterface $timeframeRepository,
        private OrderRepositoryInterface     $orderRepository,
        private EntityManagerInterface       $em,
        private ValidatorInterface           $validator
    ) {}

    public function list(): array { return $this->repository->findAll(); }

    public function get(int $id): Position
    {
        $p = $this->repository->find($id);
        if (!$p) throw new PositionNotFoundException('Position not found');
        return $p;
    }

    public function create(PositionInput $input): Position
    {
        $violations = $this->validator->validate($input);
        if (count($violations) > 0) throw new PositionValidationException($violations);

        $asset = $this->assetRepository->find($input->assetId);
        if (!$asset) throw new AssetNotFoundException();

        $timeframe = $this->timeframeRepository->find($input->timeframeId);
        if (!$timeframe) throw new TimeframeNotFoundException();

        $originOrder = null;
        if ($input->originOrderId) {
            $originOrder = $this->orderRepository->find($input->originOrderId);
            if (!$originOrder) throw new OrderNotFoundException('Origin order not found');
        }

        $position = new Position();
        $position->setAsset($asset);
        $position->setTimeframe($timeframe);
        $position->setOriginOrder($originOrder);
        $position->setOpenedAt(new DateTimeImmutable($input->openedAt));
        $position->setClosedAt($input->closedAt ? new DateTimeImmutable($input->closedAt) : null);
        $position->setDirection($input->direction);
        $position->setEntryPrice($input->entryPrice);
        $position->setExitPrice($input->exitPrice);
        $position->setStopLoss($input->stopLoss);
        $position->setTakeProfit($input->takeProfit);
        $position->setVolume($input->volume);
        $position->setRiskAmount($input->riskAmount);
        $position->setPnl($input->pnl);
        $position->setPnlPercent($input->pnlPercent);
        $position->setRr($input->rr);
        $position->setComment($input->comment);
        $position->setIsBacktest($input->isBacktest);

        $this->em->persist($position);
        $this->em->flush();
        return $position;
    }

    public function close(int $id, PositionCloseInput $input): Position
    {
        $violations = $this->validator->validate($input);
        if (count($violations) > 0) throw new PositionValidationException($violations);

        $position = $this->get($id);
        $position->setClosedAt(new DateTimeImmutable($input->closedAt));
        $position->setExitPrice($input->exitPrice);
        if ($input->pnl !== null)        $position->setPnl($input->pnl);
        if ($input->pnlPercent !== null) $position->setPnlPercent($input->pnlPercent);
        if ($input->rr !== null)         $position->setRr($input->rr);

        $this->em->flush();
        return $position;
    }

    public function delete(int $id): void
    {
        $position = $this->get($id);
        $this->em->remove($position);
        $this->em->flush();
    }
}
