<?php

namespace App\Domain\Service\Position;

use App\Domain\Exception\NotFoundException\AssetNotFoundException;
use App\Domain\Exception\NotFoundException\OrderNotFoundException;
use App\Domain\Exception\NotFoundException\PositionNotFoundException;
use App\Domain\Exception\NotFoundException\TimeframeNotFoundException;
use App\Domain\Exception\ValidationException\PositionValidationException;
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

    public function list(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @throws PositionNotFoundException
     */
    public function get(int $id): Position
    {
        $position = $this->repository->find($id);
        if (null === $position) {
            throw new PositionNotFoundException('Position not found');
        }
        return $position;
    }

    /**
     * @throws PositionValidationException
     * @throws AssetNotFoundException
     * @throws TimeframeNotFoundException
     * @throws OrderNotFoundException
     */
    public function create(PositionInput $input): Position
    {
        $violations = $this->validator->validate($input);
        if (count($violations) > 0) {
            throw new PositionValidationException($violations);
        }

        $asset = $this->assetRepository->find($input->assetId);
        if (!$asset) { throw new AssetNotFoundException(); }

        $timeframe = $this->timeframeRepository->find($input->timeframeId);
        if (!$timeframe) { throw new TimeframeNotFoundException(); }

        $originOrder = null;
        if ($input->originOrderId) {
            $originOrder = $this->orderRepository->find($input->originOrderId);
            if (!$originOrder) { throw new OrderNotFoundException('Origin order not found'); }
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

        $this->em->persist($position);
        $this->em->flush();

        return $position;
    }
}
