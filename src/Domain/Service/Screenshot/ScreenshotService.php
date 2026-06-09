<?php
namespace App\Domain\Service\Screenshot;

use App\Domain\Exception\NotFoundException\AssetNotFoundException;
use App\Domain\Exception\NotFoundException\ChartObservationNotFoundException;
use App\Domain\Exception\NotFoundException\ScreenshotNotFoundException;
use App\Domain\Exception\NotFoundException\TimeframeNotFoundException;
use App\Domain\Exception\ValidationException\ScreenshotValidationException;
use App\Domain\Service\Screenshot\ScreenshotStorage\ScreenshotStorageInterface;
use App\DTO\Screenshot\ScreenshotInput;
use App\Entity\Asset;
use App\Entity\Screenshot;
use App\Entity\Timeframe;
use App\Repository\Asset\AssetRepositoryInterface;
use App\Repository\ChartObservation\ChartObservationRepositoryInterface;
use App\Repository\Screenshot\ScreenshotRepositoryInterface;
use App\Repository\Timeframe\TimeframeRepositoryInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use DateTimeImmutable;

class ScreenshotService implements ScreenshotServiceInterface
{
    public function __construct(
        private ScreenshotRepositoryInterface        $repository,
        private AssetRepositoryInterface             $assetRepository,
        private TimeframeRepositoryInterface         $timeframeRepository,
        private ChartObservationRepositoryInterface  $observationRepository,
        private EntityManagerInterface               $em,
        private ValidatorInterface                   $validator,
        private ScreenshotStorageInterface           $screenshotStorage
    ) {}

    public function list(): array { return $this->repository->findAll(); }

    public function get(int $id): Screenshot
    {
        $s = $this->repository->find($id);
        if (!$s) throw new ScreenshotNotFoundException('Screenshot not found');
        return $s;
    }

    public function create(ScreenshotInput $input): Screenshot
    {
        $violations = $this->validator->validate($input);
        if (count($violations) > 0)
            throw new ScreenshotValidationException($violations);

        $asset = $this->assetRepository->find($input->assetId);
        if (!$asset) throw new AssetNotFoundException();

        $timeframe = $this->timeframeRepository->find($input->timeframeId);
        if (!$timeframe) throw new TimeframeNotFoundException();

        $observation = $this->observationRepository->find($input->observationId);
        if (!$observation) throw new ChartObservationNotFoundException('Observation not found');

        $binary = base64_decode($input->imageData, true);
        if ($binary === false)
            throw new ScreenshotValidationException(new ConstraintViolationList(), 'Invalid base64 image');

        $storageKey = sprintf('%s/%s/%s.png',
            $asset->getSymbol(), $timeframe->getLabel(), uniqid());
        $this->screenshotStorage->store($storageKey, $binary, $input->imageMime);

        $screenshot = new Screenshot();
        $screenshot->setFilePath($storageKey);
        $screenshot->setCreatedAt(new DateTimeImmutable($input->createdAt));
        $screenshot->setAsset($asset);
        $screenshot->setTimeframe($timeframe);
        $screenshot->setObservation($observation);
        $screenshot->setDescription($input->description);
        $screenshot->setPeriodStart(new DateTimeImmutable($input->periodStart));
        $screenshot->setPeriodEnd(new DateTimeImmutable($input->periodEnd));
        $screenshot->setSource($input->source);

        try {
            $this->em->persist($screenshot);
            $this->em->flush();
        } catch (UniqueConstraintViolationException) {
            throw new FilePathAlreadyExistsException($storageKey.' storage key already exists');
        }

        return $screenshot;
    }
}
