<?php

namespace App\Domain\Service\Position;

use App\DTO\Position\PositionCloseInput;
use App\DTO\Position\PositionInput;
use App\Entity\Position;
use App\Repository\Asset\AssetRepositoryInterface;
use App\Repository\Order\OrderRepositoryInterface;
use App\Repository\Position\PositionRepositoryInterface;
use App\Repository\Tag\TagRepositoryInterface;
use App\Repository\Timeframe\TimeframeRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class PositionService implements PositionServiceInterface
{
    public function __construct(
        private AssetRepositoryInterface     $assetRepository,
        private TimeframeRepositoryInterface $timeframeRepository,
        private OrderRepositoryInterface     $orderRepository,
        private PositionRepositoryInterface  $positionRepository,
        private TagRepositoryInterface       $tagRepository,
        private EntityManagerInterface       $em
    ) {}

    public function list(): array
    {
        return $this->positionRepository->findAll();
    }

    public function get(int $id): Position
    {
        return $this->positionRepository->find($id)
            ?? throw new \InvalidArgumentException("Position $id introuvable.");
    }

    public function create(PositionInput $input): Position
    {
        $asset = $this->assetRepository->find($input->assetId)
            ?? throw new \InvalidArgumentException("Asset {$input->assetId} introuvable.");
        $timeframe = $this->timeframeRepository->find($input->timeframeId)
            ?? throw new \InvalidArgumentException("Timeframe {$input->timeframeId} introuvable.");

        $position = new Position();
        $position->setAsset($asset);
        $position->setTimeframe($timeframe);
        $position->setDirection($input->direction);
        $position->setOpenedAt(new DateTimeImmutable($input->openedAt));
        $position->setEntryPrice($input->entryPrice);
        $position->setIsBacktest($input->isBacktest ?? false);

        if ($input->stopLoss)      $position->setStopLoss($input->stopLoss);
        if ($input->takeProfit)    $position->setTakeProfit($input->takeProfit);
        if ($input->volume)        $position->setVolume($input->volume);

        if (!empty($input->originOrderId)) {
            $order = $this->orderRepository->find($input->originOrderId);
            if ($order) $position->setOriginOrder($order);
        }

        // Tags de session assignes a la creation (ex: depuis BacktestPanel)
        if (!empty($input->tagIds)) {
            foreach ($input->tagIds as $tagId) {
                $tag = $this->tagRepository->find((int) $tagId);
                if ($tag) $position->addTag($tag);
            }
        }

        $this->em->persist($position);
        $this->em->flush();
        return $position;
    }

    // Signature conforme a PositionServiceInterface::close(int $id, PositionCloseInput $input)
    public function close(int $id, PositionCloseInput $input): Position
    {
        $position = $this->positionRepository->find($id)
            ?? throw new \InvalidArgumentException("Position $id introuvable.");

        $position->setExitPrice($input->exitPrice);
        $position->setClosedAt(new DateTimeImmutable($input->closedAt));
        if ($input->pnl !== null) $position->setPnl((string) $input->pnl);
        if (isset($input->rr) && $input->rr !== null) $position->setRr((string) $input->rr);

        $position->setClosedAt(new DateTimeImmutable($input->closedAt));
        if ($input->exitPrice !== null) $position->setExitPrice((string) $input->exitPrice);
        if ($input->pnl !== null)       $position->setPnl((string) $input->pnl);
        if (isset($input->rr) && $input->rr !== null) {
            $position->setRr((string) $input->rr);
        } else {
            // L'EA n'a pas envoye rr : le calculer depuis les champs de la position
            $entry = floatval($position->getEntryPrice());
            $exit  = floatval($position->getExitPrice());
            $sl    = floatval($position->getStopLoss());
            $slDist = abs($entry - $sl);
            if ($slDist > 0) {
                $position->setRr((string) round(abs($exit - $entry) / $slDist, 2));
            }
        }

        $this->em->flush();
        return $position;
    }

    public function delete(int $id): void
    {
        $position = $this->positionRepository->find($id);
        if (!$position) return;
        $order = $position->getOriginOrder();
        $this->em->remove($position);
        if ($order) $this->em->remove($order);
        $this->em->flush();
    }
}
