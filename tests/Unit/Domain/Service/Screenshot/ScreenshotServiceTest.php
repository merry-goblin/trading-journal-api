<?php

namespace App\Tests\Unit\Domain\Service\Screenshot;

use App\Domain\Exception\NotFoundException\ScreenshotNotFoundException;
use App\Domain\Service\Screenshot\FilePathAlreadyExistsException;
use App\Domain\Service\Screenshot\ScreenshotService;
use App\Domain\Service\Screenshot\ScreenshotStorage\ScreenshotStorageInterface;
use App\DTO\Screenshot\ScreenshotInput;
use App\Entity\Asset;
use App\Entity\ChartObservation;
use App\Entity\Order;
use App\Entity\Position;
use App\Entity\Screenshot;
use App\Entity\Timeframe;
use App\Repository\Asset\AssetRepositoryInterface;
use App\Repository\Screenshot\ScreenshotRepositoryInterface;
use App\Repository\Timeframe\TimeframeRepositoryInterface;
use App\Repository\ChartObservation\ChartObservationRepositoryInterface;
use DateTimeImmutable;
use Doctrine\DBAL\Driver\Exception as DriverException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

//use App\Domain\Service\Screenshot\SymbolAlreadyExistsException;

class ScreenshotServiceTest extends TestCase
{
    private string $base64Image = 'iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgALgAD/aNpbtEAAAAASUVORK5CYII';

    /* get method */

    public function testGetOneScreenshotById(): void
    {
        // Mock data
        $expectedAsset = $this->createAsset(1, 'EURUSD', 'forex', '');
        $expectedTimeframe = $this->createTimeframe(1, 'M1', 60);
        $expectedScreenshot = $this->createScreenshot(
            1,
            'C:\Users\kelle\AppData\Roaming\MetaQuotes\Terminal\D0E8209F77C8CF37AD8BF550E51FF075\MQL5\Files\EURUSD_H4_2025-12-17_01-58-38.png',
            new DateTimeImmutable('2025-12-29 00:14:00'),
            $expectedAsset,
            $expectedTimeframe,
            null,
            '',
            new DateTimeImmutable('2025-11-25 00:00:00'),
            new DateTimeImmutable('2025-12-17 01:58:38'),
            'manual'
        );

        // Dependency injections
        $screenshotRepository = $this->createMock(ScreenshotRepositoryInterface::class);
        $screenshotRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($expectedScreenshot)
        ;
        $assetRepository = $this->createStub(AssetRepositoryInterface::class);
        $timeframeRepository = $this->createStub(TimeframeRepositoryInterface::class);
        $chartObservationRepository = $this->createStub(ChartObservationRepositoryInterface::class);
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);
        $screenshotStorage = $this->createStub(ScreenshotStorageInterface::class);

        // Start test
        $screenshotService = new ScreenshotService($screenshotRepository, $assetRepository, $timeframeRepository, $chartObservationRepository, $em, $validator, $screenshotStorage);
        $screenshot = $screenshotService->get(1);

        // Assertions
        $this->assertInstanceOf(Screenshot::class, $screenshot);
        $this->assertSame($expectedScreenshot, $screenshot);
        $this->assertSame($expectedAsset, $screenshot->getAsset());
        $this->assertSame($expectedTimeframe, $screenshot->getTimeframe());
    }

    public function testGetOneScreenshotByIdNotFound(): void
    {
        // Mock data
        $expected = null;

        // Dependency injections
        $screenshotRepository = $this->createMock(ScreenshotRepositoryInterface::class);
        $screenshotRepository->expects(self::once())
            ->method('find')
            ->with(9999)
            ->willReturn($expected)
        ;
        $assetRepository = $this->createStub(AssetRepositoryInterface::class);
        $timeframeRepository = $this->createStub(TimeframeRepositoryInterface::class);
        $chartObservationRepository = $this->createStub(ChartObservationRepositoryInterface::class);
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);
        $screenshotStorage = $this->createStub(ScreenshotStorageInterface::class);

        // Assertions
        $this->expectException(ScreenshotNotFoundException::class);
        $this->expectExceptionMessage('Screenshot not found');

        // Start test
        $screenshotService = new ScreenshotService($screenshotRepository, $assetRepository, $timeframeRepository, $chartObservationRepository, $em, $validator, $screenshotStorage);
        $screenshotService->get(9999);
    }

    /* list method */

    public function testListWithMultipleScreenshots(): void
    {
        // Mock data
        $expectedAsset1 = $this->createAsset(1, 'EURUSD', 'forex', '');
        $expectedAsset2 = $this->createAsset(2, 'GBPUSD', 'forex', '');
        $expectedTimeframe1 = $this->createTimeframe(1, 'M1', 60);
        $expectedTimeframe2 = $this->createTimeframe(2, 'M5', 300);
        $expectedScreenshot1 = $this->createScreenshot(
            1,
            'C:\Users\kelle\AppData\Roaming\MetaQuotes\Terminal\D0E8209F77C8CF37AD8BF550E51FF075\MQL5\Files\EURUSD_H4_2025-12-17_01-58-38.png',
            new DateTimeImmutable('2025-12-29 00:14:00'),
            $expectedAsset1,
            $expectedTimeframe1,
            null,
            '',
            new DateTimeImmutable('2025-11-25 00:00:00'),
            new DateTimeImmutable('2025-12-17 01:58:38'),
            'manual'
        );
        $expectedScreenshot2 = $this->createScreenshot(
            2,
            'C:\Users\kelle\AppData\Roaming\MetaQuotes\Terminal\D0E8209F77C8CF37AD8BF550E51FF075\MQL5\Files\EURUSD_H4_2025-12-18_08-35-49.png',
            new DateTimeImmutable('2025-12-29 00:18:00'),
            $expectedAsset2,
            $expectedTimeframe2,
            null,
            '',
            new DateTimeImmutable('2025-12-18 00:00:00'),
            new DateTimeImmutable('2025-12-21 08:35:49'),
            'manual'
        );
        $expectedList = [
            $expectedScreenshot1,
            $expectedScreenshot2,
        ];

        // Dependency injections
        $screenshotRepository = $this->createMock(ScreenshotRepositoryInterface::class);
        $screenshotRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($expectedList)
        ;
        $assetRepository = $this->createStub(AssetRepositoryInterface::class);
        $timeframeRepository = $this->createStub(TimeframeRepositoryInterface::class);
        $chartObservationRepository = $this->createStub(ChartObservationRepositoryInterface::class);
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);
        $screenshotStorage = $this->createStub(ScreenshotStorageInterface::class);

        // Start test
        $screenshotService = new ScreenshotService($screenshotRepository, $assetRepository, $timeframeRepository, $chartObservationRepository, $em, $validator, $screenshotStorage);
        $screenshotList = $screenshotService->list();

        // Assertions
        $this->assertIsArray($screenshotList);
        $this->assertCount(count($expectedList), $screenshotList);
        $this->assertContainsOnlyInstancesOf(Screenshot::class, $screenshotList);
    }

    public function testListWithNoScreenshot(): void
    {
        // Mock data
        $expectedList = [];

        // Dependency injections
        $screenshotRepository = $this->createMock(ScreenshotRepositoryInterface::class);
        $screenshotRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($expectedList)
        ;
        $assetRepository = $this->createStub(AssetRepositoryInterface::class);
        $timeframeRepository = $this->createStub(TimeframeRepositoryInterface::class);
        $chartObservationRepository = $this->createStub(ChartObservationRepositoryInterface::class);
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);
        $screenshotStorage = $this->createStub(ScreenshotStorageInterface::class);

        // Start test
        $screenshotService = new ScreenshotService($screenshotRepository, $assetRepository, $timeframeRepository, $chartObservationRepository, $em, $validator, $screenshotStorage);
        $screenshotList = $screenshotService->list();

        // Assertions
        $this->assertIsArray($screenshotList);
        $this->assertCount(count($expectedList), $screenshotList);
    }

    /* create method */

    public function testCreateScreenshot(): void
    {
        // Mock data
        $asset = $this->createAsset(1, 'EURUSD', 'forex', '');
        $timeframe = $this->createTimeframe(3, 'M1', 60);
        $chartObservation = $this->createChartObservation(
            5, $asset, $timeframe, new DateTimeImmutable('2025-12-29 00:14:00'), 'bull', '', null, null
        );
        $input = $this->createScreenshotInput(
            '2025-12-29 00:14:00',
            1,
            3,
            5,
            '',
            '2025-11-25 00:00:00',
            '2025-12-17 01:58:38',
            'manual',
            $this->base64Image,
            'image/png'
        );

        // Dependency injection
        $screenshotRepository = $this->createStub(ScreenshotRepositoryInterface::class);
        $assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $assetRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($asset)
        ;
        $timeframeRepository = $this->createMock(TimeframeRepositoryInterface::class);
        $timeframeRepository->expects(self::once())
            ->method('find')
            ->with(3)
            ->willReturn($timeframe)
        ;
        $chartObservationRepository = $this->createMock(ChartObservationRepositoryInterface::class);
        $chartObservationRepository->expects(self::once())
            ->method('find')
            ->with(5)
            ->willReturn($chartObservation)
        ;
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())
            ->method('persist')
        ;
        $em->expects(self::once())
            ->method('flush')
        ;
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList())
        ;
        $screenshotStorage = $this->createMock(ScreenshotStorageInterface::class);
        $screenshotStorage->expects(self::once())
            ->method('store')
        ;

        // Start test
        $screenshotService = new ScreenshotService($screenshotRepository, $assetRepository, $timeframeRepository, $chartObservationRepository, $em, $validator, $screenshotStorage);
        $screenshot = $screenshotService->create($input);

        // Assertions
        $this->assertIsObject($screenshot);
        $this->assertInstanceOf(Screenshot::class, $screenshot);
        $this->assertMatchesRegularExpression('#EURUSD/M1/[a-z\d]*.png#i', $screenshot->getFilePath());
        $this->assertSame($input->createdAt, $screenshot->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertSame($asset, $screenshot->getAsset());
        $this->assertSame($timeframe, $screenshot->getTimeframe());
        $this->assertSame($chartObservation, $screenshot->getObservation());
    }

    public function testCreateScreenshotWithFilePathDuplication(): void
    {
        // Mock data
        $asset = $this->createAsset(1, 'EURUSD', 'forex', '');
        $timeframe = $this->createTimeframe(3, 'M1', 60);
        $chartObservation = $this->createChartObservation(
            5, $asset, $timeframe, new DateTimeImmutable('2025-12-29 00:14:00'), 'bull', '', null, null
        );
        $input = $this->createScreenshotInput(
            '2025-12-29 00:14:00',
            1,
            3,
            5,
            '',
            '2025-11-25 00:00:00',
            '2025-12-17 01:58:38',
            'manual',
            $this->base64Image,
            'image/png'
        );
        $exception = new UniqueConstraintViolationException(
            $this->createStub(DriverException::class),
            null
        );

        // Dependency injection
        $screenshotRepository = $this->createStub(ScreenshotRepositoryInterface::class);
        $assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $assetRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($asset)
        ;
        $timeframeRepository = $this->createMock(TimeframeRepositoryInterface::class);
        $timeframeRepository->expects(self::once())
            ->method('find')
            ->with(3)
            ->willReturn($timeframe)
        ;
        $chartObservationRepository = $this->createMock(ChartObservationRepositoryInterface::class);
        $chartObservationRepository->expects(self::once())
            ->method('find')
            ->with(5)
            ->willReturn($chartObservation)
        ;
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())
            ->method('persist')
        ;
        $em->expects(self::once())
            ->method('flush')
            ->willThrowException($exception)
        ;
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList())
        ;
        $screenshotStorage = $this->createStub(ScreenshotStorageInterface::class);

        // Assertions
        $this->expectException(FilePathAlreadyExistsException::class);

        // Start test
        $screenshotService = new ScreenshotService($screenshotRepository, $assetRepository, $timeframeRepository, $chartObservationRepository, $em, $validator, $screenshotStorage);
        $screenshotService->create($input);
    }

    /*public function testCreateScreenshotWithInvalidPayloadThrowsException(): void
    {
        // Mock data
        $input = $this->createScreenshotInput('', '', '');
        $violations = new ConstraintViolationList([
            new ConstraintViolation(
                message: 'This value should not be blank.',
                messageTemplate: null,
                parameters: [],
                root: $input,
                propertyPath: 'symbol',
                invalidValue: '',
            ),
        ]);

        // Dependency injection
        $screenshotRepository = $this->createStub(ScreenshotRepositoryInterface::class);
        $assetRepository = $this->createStub(AssetRepositoryInterface::class);
        $timeframeRepository = $this->createStub(TimeframeRepositoryInterface::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())
            ->method('persist')
        ;
        $em->expects(self::never())
            ->method('flush')
        ;
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn($violations)
        ;

        // Assertions
        $this->expectException(ScreenshotValidationException::class);

        // Start test
        $screenshotService = new ScreenshotService($screenshotRepository, $assetRepository, $timeframeRepository, $em, $validator, $screenshotStorage);
        $screenshotService->create($input);
    }*/

    /* private methods */

    private function createScreenshot(
        int $id,
        string $filePath,
        DateTimeImmutable $createdAt,
        Asset $asset,
        Timeframe $timeframe,
        ?ChartObservation $observation,
        string $description,
        DateTimeImmutable $periodStart,
        DateTimeImmutable $periodEnd,
        string $source
    ): Screenshot {
        $screenshot = new Screenshot();
        $screenshot->setId($id);
        $screenshot->setFilePath($filePath);
        $screenshot->setCreatedAt($createdAt);
        $screenshot->setAsset($asset);
        $screenshot->setTimeframe($timeframe);
        $screenshot->setObservation($observation);
        $screenshot->setDescription($description);
        $screenshot->setPeriodStart($periodStart);
        $screenshot->setPeriodEnd($periodEnd);
        $screenshot->setSource($source);

        return $screenshot;
    }

    private function createAsset(
        int $id,
        string $symbol,
        string $type,
        string $description
    ): Asset {
        $asset = new Asset();
        $asset->setId($id);
        $asset->setSymbol($symbol);
        $asset->setType($type);
        $asset->setDescription($description);

        return $asset;
    }

    private function createTimeframe(
        int $id,
        string $label,
        int $seconds
    ): Timeframe {
        $timeframe = new Timeframe();
        $timeframe->setId($id);
        $timeframe->setLabel($label);
        $timeframe->setSeconds($seconds);

        return $timeframe;
    }

    private function createChartObservation(
        int $id,
        Asset $asset,
        Timeframe $timeframe,
        DateTimeImmutable $observedAt,
        string $trend,
        string $comment,
        ?Order $order,
        ?Position $position
    ): ChartObservation {
        $chartObservation = new ChartObservation();
        $chartObservation->setId($id);
        $chartObservation->setAsset($asset);
        $chartObservation->setTimeframe($timeframe);
        $chartObservation->setObservedAt($observedAt);
        $chartObservation->setTrend($trend);
        $chartObservation->setComment($comment);
        $chartObservation->setOrder($order);
        $chartObservation->setPosition($position);

        return $chartObservation;
    }

    private function createScreenshotInput(
        string $createdAt,
        int $assetId,
        int $timeframeId,
        ?int $observationId,
        string $description,
        string $periodStart,
        string $periodEnd,
        string $source,
        string $imageData,
        string $imageMime
    ): ScreenshotInput {
        $screenshotInput = new ScreenshotInput();
        $screenshotInput->createdAt = $createdAt;
        $screenshotInput->assetId = $assetId;
        $screenshotInput->timeframeId = $timeframeId;
        $screenshotInput->observationId = $observationId;
        $screenshotInput->description = $description;
        $screenshotInput->periodStart = $periodStart;
        $screenshotInput->periodEnd = $periodEnd;
        $screenshotInput->source = $source;
        $screenshotInput->imageData = $imageData;
        $screenshotInput->imageMime = $imageMime;

        return $screenshotInput;
    }
}
