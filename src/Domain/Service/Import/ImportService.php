<?php

namespace App\Domain\Service\Import;

use App\DTO\FrontApi\Import\ImportPositionItem;
use App\Entity\Asset;
use App\Entity\Position;
use App\Repository\Asset\AssetRepositoryInterface;
use App\Repository\Position\PositionRepositoryInterface;
use App\Repository\Timeframe\TimeframeRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class ImportService implements ImportServiceInterface
{
    public function __construct(
        private AssetRepositoryInterface     $assetRepository,
        private TimeframeRepositoryInterface $timeframeRepository,
        private PositionRepositoryInterface  $positionRepository,
        private EntityManagerInterface       $em
    ) {}

    public function importPositions(array $items, int $timeframeId): array
    {
        $timeframe = $this->timeframeRepository->find($timeframeId);
        if (!$timeframe) {
            return ['created' => 0, 'skipped' => 0, 'errors' => ['Timeframe introuvable.']];
        }

        $created = 0;
        $skipped = 0;
        $errors  = [];

        foreach ($items as $index => $item) {
            try {
                // Cle de deduplication : symbol + direction + openedAt + entryPrice
                if ($this->positionRepository->existsByKey(
                    $item->symbol,
                    $item->direction,
                    $item->openedAt,
                    $item->entryPrice
                )) {
                    $skipped++;
                    continue;
                }

                $asset = $this->resolveAsset($item->symbol);

                $position = new Position();
                $position->setAsset($asset);
                $position->setTimeframe($timeframe);
                $position->setDirection($item->direction);
                $position->setOpenedAt(new DateTimeImmutable($item->openedAt));
                $position->setClosedAt(new DateTimeImmutable($item->closedAt));
                $position->setEntryPrice($item->entryPrice);
                $position->setExitPrice($item->exitPrice);
                $position->setVolume($item->volume);
                $position->setPnl($item->pnl);
                if ($item->stopLoss)   $position->setStopLoss($item->stopLoss);
                if ($item->takeProfit) $position->setTakeProfit($item->takeProfit);

                $this->em->persist($position);
                $created++;
            } catch (\Throwable $e) {
                $errors[] = sprintf('Ligne %d (%s) : %s', $index + 1, $item->symbol, $e->getMessage());
            }
        }

        $this->em->flush();
        return ['created' => $created, 'skipped' => $skipped, 'errors' => $errors];
    }

    private function resolveAsset(string $symbol): Asset
    {
        $asset = $this->assetRepository->findOneBy(['symbol' => $symbol]);
        if ($asset) return $asset;

        $asset = new Asset();
        $asset->setSymbol($symbol);
        $asset->setType('cfd');
        $asset->setDescription('');
        $this->em->persist($asset);
        return $asset;
    }
}
