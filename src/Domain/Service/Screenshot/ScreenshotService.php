<?php

namespace App\Domain\Service\Screenshot;

use App\Domain\Exception\NotFoundException\AssetNotFoundException;
use App\Domain\Exception\NotFoundException\ScreenshotNotFoundException;
use App\Domain\Exception\NotFoundException\TimeframeNotFoundException;
use App\Domain\Exception\ValidationException\ScreenshotValidationException;
use App\Domain\Service\Screenshot\ScreenshotStorage\ScreenshotStorageInterface;
use App\DTO\Screenshot\ScreenshotInput;
use App\Entity\Asset;
use App\Entity\Screenshot;
use App\Entity\Timeframe;
use App\Repository\Asset\AssetRepositoryInterface;
use App\Repository\Screenshot\ScreenshotRepositoryInterface;
use App\Repository\Timeframe\TimeframeRepositoryInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use DateTimeImmutable;
use SplFileObject;

class ScreenshotService implements ScreenshotServiceInterface
{
    public function __construct(
        private ScreenshotRepositoryInterface $repository,
        private AssetRepositoryInterface $assetRepository,
        private TimeframeRepositoryInterface $timeframeRepository,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private ScreenshotStorageInterface $screenshotStorage
    ) {}

    public function list(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @throws ScreenshotNotFoundException
     */
    public function get(int $id): Screenshot
    {
        $screenshot = $this->repository->find($id);
        if (null === $screenshot) {
            throw new ScreenshotNotFoundException('Screenshot not found');
        }
        return $screenshot;
    }

    /**
     * @throws FilePathAlreadyExistsException
     */
    public function create(
        ScreenshotInput $input,
        SplFileObject $file,
        ?string $mimeType
    ): Screenshot
    {
        // Validation
        $violations = $this->validator->validate($input);
        if (count($violations) > 0) {
            throw new ScreenshotValidationException($violations);
        }

        // Related entities
        $asset = $this->assetRepository->find($input->assetId);
        if (!$asset) {
            throw new AssetNotFoundException();
        }
        $timeframe = $this->timeframeRepository->find($input->timeframeId);
        if (!$timeframe) {
            throw new TimeframeNotFoundException();
        }

        $storageKey = $this->storeScreenshotFile($file, $mimeType, $asset, $timeframe); // Screenshot storage

        $screenshot = new Screenshot();
        $screenshot->setFilePath($storageKey);
        $screenshot->setCreatedAt(new DateTimeImmutable($input->createdAt));
        $screenshot->setAsset($asset);
        $screenshot->setTimeframe($timeframe);
        $screenshot->setDescription($input->description);
        $screenshot->setPeriodStart(new DateTimeImmutable($input->periodStart));
        $screenshot->setPeriodEnd(new DateTimeImmutable($input->periodEnd));
        $screenshot->setSource($input->source);

        try {
            $this->em->persist($screenshot);
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new FilePathAlreadyExistsException($input->filePath.' file path already exists');
        }

        return $screenshot;
    }

    private function storeScreenshotFile(
        SplFileObject $file,
        ?string $mimeType,
        Asset $asset,
        Timeframe $timeframe
    )
    {
        $storageKey = sprintf(
            '%s/%s/%s.png',
            $asset->getSymbol(),
            $timeframe->getLabel(),
            time()
        );

        $this->screenshotStorage->store($storageKey, $file, $mimeType);

        return $storageKey;
    }
}
