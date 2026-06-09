<?php
namespace App\Domain\Service\ChartObservation;

use App\Domain\Exception\NotFoundException\AssetNotFoundException;
use App\Domain\Exception\NotFoundException\ChartObservationNotFoundException;
use App\Domain\Exception\NotFoundException\OrderNotFoundException;
use App\Domain\Exception\NotFoundException\PositionNotFoundException;
use App\Domain\Exception\NotFoundException\TimeframeNotFoundException;
use App\Domain\Exception\ValidationException\ChartObservationValidationException;
use App\Domain\Service\Screenshot\ScreenshotStorage\ScreenshotStorageInterface;
use App\DTO\ChartObservation\ChartObservationInput;
use App\Entity\ChartObservation;
use App\Entity\Screenshot;
use App\Repository\Asset\AssetRepositoryInterface;
use App\Repository\ChartObservation\ChartObservationRepositoryInterface;
use App\Repository\Order\OrderRepositoryInterface;
use App\Repository\Position\PositionRepositoryInterface;
use App\Repository\Timeframe\TimeframeRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChartObservationService implements ChartObservationServiceInterface
{
    public function __construct(
        private ChartObservationRepositoryInterface $repository,
        private AssetRepositoryInterface            $assetRepository,
        private TimeframeRepositoryInterface        $timeframeRepository,
        private OrderRepositoryInterface            $orderRepository,
        private PositionRepositoryInterface         $positionRepository,
        private EntityManagerInterface              $em,
        private ValidatorInterface                  $validator,
        private ScreenshotStorageInterface          $screenshotStorage
    ) {}

    public function list(): array { return $this->repository->findAll(); }

    public function get(int $id): ChartObservation
    {
        $obs = $this->repository->find($id);
        if (!$obs) throw new ChartObservationNotFoundException('Observation not found');
        return $obs;
    }

    public function create(ChartObservationInput $input): ChartObservation
    {
        $violations = $this->validator->validate($input);
        if (count($violations) > 0)
            throw new ChartObservationValidationException($violations);

        $asset = $this->assetRepository->find($input->assetId);
        if (!$asset) throw new AssetNotFoundException();

        $timeframe = $this->timeframeRepository->find($input->timeframeId);
        if (!$timeframe) throw new TimeframeNotFoundException();

        $order = null;
        if ($input->orderId) {
            $order = $this->orderRepository->find($input->orderId);
            if (!$order) throw new OrderNotFoundException('Order not found');
        }

        $position = null;
        if ($input->positionId) {
            $position = $this->positionRepository->find($input->positionId);
            if (!$position) throw new PositionNotFoundException('Position not found');
        }

        $observation = new ChartObservation();
        $observation->setAsset($asset);
        $observation->setTimeframe($timeframe);
        $observation->setObservedAt(new DateTimeImmutable($input->observedAt));
        $observation->setTrend($input->trend);
        $observation->setComment($input->comment);
        $observation->setOrder($order);
        $observation->setPosition($position);

        $this->em->persist($observation);

        // Screenshot optionnel inline
        if ($input->imageData !== null && $input->periodStart !== null && $input->periodEnd !== null) {
            $binary = base64_decode($input->imageData, true);
            if ($binary !== false) {
                $storageKey = sprintf('%s/%s/%s.png',
                    $asset->getSymbol(), $timeframe->getLabel(), uniqid());
                $this->screenshotStorage->store($storageKey, $binary, $input->imageMime ?? 'image/png');

                $screenshot = new Screenshot();
                $screenshot->setFilePath($storageKey);
                $screenshot->setCreatedAt(new DateTimeImmutable($input->observedAt));
                $screenshot->setAsset($asset);
                $screenshot->setTimeframe($timeframe);
                $screenshot->setObservation($observation);
                $screenshot->setSource('auto');
                $screenshot->setPeriodStart(new DateTimeImmutable($input->periodStart));
                $screenshot->setPeriodEnd(new DateTimeImmutable($input->periodEnd));
                $this->em->persist($screenshot);
            }
        }

        $this->em->flush();
        return $observation;
    }
}
