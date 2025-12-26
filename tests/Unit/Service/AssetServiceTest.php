<?php

namespace App\Tests\Service;

use App\Domain\Exception\NotFoundException\AssetNotFoundException;
use PHPUnit\Framework\TestCase;

use App\DTO\Asset\AssetInput;
use App\Entity\Asset;

use App\Service\Asset\AssetService;
use App\Repository\AssetRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Service\Asset\SymbolAlreadyExistsException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Driver\Exception as DriverException;
use App\Domain\Exception\ValidationException\AssetValidationException;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AssetServiceTest extends TestCase
{
    /* get method */

    public function testGetOneAssetById(): void
    {
        // Mock data
        $expected = $this->createAsset(1, 'EURUSD', 'forex', '');

        // Dependancy injections
        $assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $assetRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($expected)
        ;
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);

        // Start test
        $assetService = new AssetService($assetRepository, $em, $validator);
        $asset = $assetService->get(1);

        // Assertions
        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame($expected, $asset);
    }

    public function testGetOneAssetByIdNotFound(): void
    {
        // Mock data
        $expected = null;

        // Dependancy injections
        $assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $assetRepository->expects(self::once())
            ->method('find')
            ->with(2)
            ->willReturn($expected)
        ;
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);

        // Assertions
        $this->expectException(AssetNotFoundException::class);
        $this->expectExceptionMessage('Asset not found');

        // Start test
        $assetService = new AssetService($assetRepository, $em, $validator);
        $asset = $assetService->get(2);
    }

    /* getBySymbol method */

    public function testGetBySymbolOneAsset(): void
    {
        // Mock data
        $expected = $this->createAsset(1, 'EURUSD', 'forex', '');

        // Dependancy injections
        $assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $assetRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['symbol' => 'EURUSD'])
            ->willReturn($expected)
        ;
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);

        // Start test
        $assetService = new AssetService($assetRepository, $em, $validator);
        $asset = $assetService->getBySymbol('EURUSD');

        // Assertions
        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame($expected, $asset);
    }

    public function testGetBySymbolWithSymbolNotFound(): void
    {
        // Mock data
        $expected = null;

        // Dependancy injections
        $assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $assetRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['symbol' => 'CFAUSD'])
            ->willReturn($expected)
        ;
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);

        // Assertions
        $this->expectException(AssetNotFoundException::class);
        $this->expectExceptionMessage('Asset not found');

        // Start test
        $assetService = new AssetService($assetRepository, $em, $validator);
        $asset = $assetService->getBySymbol('CFAUSD');
    }

    /* list method */

    public function testGetAllAssets(): void
    {
        // Mock data
        $expectedList = [
            $this->createAsset(1, 'EURUSD', 'forex', ''),
            $this->createAsset(2, 'EURGBP', 'forex', ''),
        ];

        // Dependancy injections
        $assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $assetRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($expectedList)
        ;
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);

        // Start test
        $assetService = new AssetService($assetRepository, $em, $validator);
        $assetList = $assetService->list();

        // Assertions
        $this->assertIsArray($assetList);
        $this->assertCount(count($expectedList), $assetList);
        $this->assertContainsOnlyInstancesOf(Asset::class, $assetList);
    }

    public function testGetAllAssetsNoData(): void
    {
        // Mock data
        $expectedList = [];

        // Dependancy injections
        $assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $assetRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($expectedList)
        ;
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);

        // Start test
        $assetService = new AssetService($assetRepository, $em, $validator);
        $assetList = $assetService->list();

        // Assertions
        $this->assertIsArray($assetList);
        $this->assertCount(count($expectedList), $assetList);
    }

    /* create method */

    public function testCreateAsset(): void
    {
        // Mock data
        $input = $this->createAssetInput('EURUSD', 'forex', '');

        // Dependancy injection
        $assetRepository = $this->createStub(AssetRepositoryInterface::class);
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

        // Start test
        $assetService = new AssetService($assetRepository, $em, $validator);
        $asset = $assetService->create($input);

        // Assertions
        $this->assertIsObject($asset);
        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame($input->symbol, $asset->getSymbol());
        $this->assertSame($input->type, $asset->getType());
        $this->assertSame($input->description, $asset->getDescription());
    }

    public function testCreateAssetWithSymbolDuplication(): void
    {
        // Mock data
        $input = $this->createAssetInput('EURUSD', 'forex', '');
        $exception = new UniqueConstraintViolationException(
            $this->createStub(DriverException::class),
            null
        );

        // Dependancy injection
        $assetRepository = $this->createStub(AssetRepositoryInterface::class);
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

        // Assertions
        $this->expectException(SymbolAlreadyExistsException::class);
        $this->expectExceptionMessage('EURUSD symbol already exists');

        // Start test
        $assetService = new AssetService($assetRepository, $em, $validator);
        $assetService->create($input);
    }

    public function testCreateAssetWithInvalidPayloadThrowsException(): void
    {
        // Mock data
        $input = $this->createAssetInput('', '', '');
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

        // Dependancy injection
        $assetRepository = $this->createStub(AssetRepositoryInterface::class);
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
        $this->expectException(AssetValidationException::class);

        // Start test
        $assetService = new AssetService($assetRepository, $em, $validator);
        $assetService->create($input);
    }

    /* private methods */

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

    private function createAssetInput(
        string $symbol,
        string $type,
        string $description
    ): AssetInput {
        $assetInput = new AssetInput();
        $assetInput->symbol = $symbol;
        $assetInput->type = $type;
        $assetInput->description = $description;

        return $assetInput;
    }
}
