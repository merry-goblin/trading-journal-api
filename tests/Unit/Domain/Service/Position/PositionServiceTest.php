<?php

namespace App\Tests\Unit\Domain\Service\Position;

use App\Domain\Exception\NotFoundException\AssetNotFoundException;
use App\Domain\Exception\NotFoundException\OrderNotFoundException;
use App\Domain\Exception\NotFoundException\PositionNotFoundException;
use App\Domain\Exception\NotFoundException\TimeframeNotFoundException;
use App\Domain\Exception\ValidationException\PositionValidationException;
use App\Domain\Service\Position\PositionService;
use App\DTO\Position\PositionInput;
use App\Entity\Asset;
use App\Entity\Order;
use App\Entity\Position;
use App\Entity\Timeframe;
use App\Repository\Asset\AssetRepositoryInterface;
use App\Repository\Order\OrderRepositoryInterface;
use App\Repository\Position\PositionRepositoryInterface;
use App\Repository\Timeframe\TimeframeRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PositionServiceTest extends TestCase
{
    /* get method */

    public function testGetOnePositionById(): void
    {
        // Mock data
        $expected = $this->createPosition(1, 'long', '7410.86', '15.00');

        // Dependency injections
        $positionRepository = $this->createMock(PositionRepositoryInterface::class);
        $positionRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($expected)
        ;

        // Start test
        $position = $this->buildService(positionRepository: $positionRepository)->get(1);

        // Assertions
        $this->assertInstanceOf(Position::class, $position);
        $this->assertSame($expected, $position);
    }

    public function testGetOnePositionByIdNotFound(): void
    {
        // Dependency injections
        $positionRepository = $this->createMock(PositionRepositoryInterface::class);
        $positionRepository->expects(self::once())
            ->method('find')
            ->with(9999)
            ->willReturn(null)
        ;

        // Assertions
        $this->expectException(PositionNotFoundException::class);
        $this->expectExceptionMessage('Position not found');

        // Start test
        $this->buildService(positionRepository: $positionRepository)->get(9999);
    }

    /* list method */

    public function testListWithMultiplePositions(): void
    {
        // Mock data
        $expectedList = [
            $this->createPosition(1, 'long',  '7410.86', '15.00'),
            $this->createPosition(2, 'short', '7530.00', '10.00'),
        ];

        // Dependency injections
        $positionRepository = $this->createMock(PositionRepositoryInterface::class);
        $positionRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($expectedList)
        ;

        // Start test
        $list = $this->buildService(positionRepository: $positionRepository)->list();

        // Assertions
        $this->assertIsArray($list);
        $this->assertCount(2, $list);
        $this->assertContainsOnlyInstancesOf(Position::class, $list);
    }

    public function testListWithNoPositions(): void
    {
        // Dependency injections
        $positionRepository = $this->createMock(PositionRepositoryInterface::class);
        $positionRepository->expects(self::once())
            ->method('findAll')
            ->willReturn([])
        ;

        // Start test
        $list = $this->buildService(positionRepository: $positionRepository)->list();

        // Assertions
        $this->assertIsArray($list);
        $this->assertCount(0, $list);
    }

    /* create method */

    public function testCreatePosition(): void
    {
        // Mock data
        $asset     = $this->createAsset(1, 'SP500');
        $timeframe = $this->createTimeframe(1, 'M5', 300);
        $input     = $this->createPositionInput(1, 1, '7410.86', '15.00', 'long');

        // Dependency injections
        $positionRepository  = $this->createStub(PositionRepositoryInterface::class);
        $assetRepository     = $this->createMock(AssetRepositoryInterface::class);
        $assetRepository->expects(self::once())->method('find')->with(1)->willReturn($asset);
        $timeframeRepository = $this->createMock(TimeframeRepositoryInterface::class);
        $timeframeRepository->expects(self::once())->method('find')->with(1)->willReturn($timeframe);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('persist');
        $em->expects(self::once())->method('flush');
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())->method('validate')->willReturn(new ConstraintViolationList());

        // Start test
        $service  = $this->buildService(
            positionRepository: $positionRepository,
            assetRepository: $assetRepository,
            timeframeRepository: $timeframeRepository,
            em: $em,
            validator: $validator
        );
        $position = $service->create($input);

        // Assertions
        $this->assertInstanceOf(Position::class, $position);
        $this->assertSame($asset,     $position->getAsset());
        $this->assertSame($timeframe, $position->getTimeframe());
        $this->assertSame('long',     $position->getDirection());
        $this->assertSame('7410.86',  $position->getEntryPrice());
        $this->assertSame('15.00',    $position->getVolume());
        $this->assertNull($position->getOriginOrder());
        // Champs generiques : null a la creation, renseignes depuis Vue.js
        $this->assertNull($position->isPlanRespected());
        $this->assertNull($position->getHigherTfBias());
        $this->assertNull($position->getEntryTfBias());
        $this->assertNull($position->getSetupQuality());
        $this->assertNull($position->getEmotionScore());
    }

    public function testCreatePositionWithOriginOrder(): void
    {
        // Mock data
        $asset     = $this->createAsset(1, 'SP500');
        $timeframe = $this->createTimeframe(1, 'M5', 300);
        $order     = $this->createOrder(42);
        $input     = $this->createPositionInput(1, 1, '7410.86', '15.00', 'long', originOrderId: 42);

        // Dependency injections
        $assetRepository     = $this->createStub(AssetRepositoryInterface::class);
        $assetRepository->method('find')->willReturn($asset);
        $timeframeRepository = $this->createStub(TimeframeRepositoryInterface::class);
        $timeframeRepository->method('find')->willReturn($timeframe);
        $orderRepository     = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->expects(self::once())->method('find')->with(42)->willReturn($order);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())->method('validate')->willReturn(new ConstraintViolationList());
        $em = $this->createStub(EntityManagerInterface::class);

        // Start test
        $position = $this->buildService(
            assetRepository: $assetRepository,
            timeframeRepository: $timeframeRepository,
            orderRepository: $orderRepository,
            em: $em,
            validator: $validator
        )->create($input);

        // Assertions
        $this->assertSame($order, $position->getOriginOrder());
    }

    public function testCreatePositionWithOriginOrderNotFoundThrowsException(): void
    {
        // Mock data
        $asset     = $this->createAsset(1, 'SP500');
        $timeframe = $this->createTimeframe(1, 'M5', 300);
        $input     = $this->createPositionInput(1, 1, '7410.86', '15.00', 'long', originOrderId: 999);

        // Dependency injections
        $assetRepository     = $this->createStub(AssetRepositoryInterface::class);
        $assetRepository->method('find')->willReturn($asset);
        $timeframeRepository = $this->createStub(TimeframeRepositoryInterface::class);
        $timeframeRepository->method('find')->willReturn($timeframe);
        $orderRepository     = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->expects(self::once())->method('find')->with(999)->willReturn(null);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())->method('validate')->willReturn(new ConstraintViolationList());

        // Assertions
        $this->expectException(OrderNotFoundException::class);
        $this->expectExceptionMessage('Origin order not found');

        // Start test
        $this->buildService(
            assetRepository: $assetRepository,
            timeframeRepository: $timeframeRepository,
            orderRepository: $orderRepository,
            validator: $validator
        )->create($input);
    }

    public function testCreatePositionWithInvalidPayloadThrowsException(): void
    {
        // Mock data
        $input      = $this->createPositionInput(0, 0, '', '', null);
        $violations = new ConstraintViolationList([
            new ConstraintViolation('This value should not be blank.', null, [], $input, 'entryPrice', ''),
        ]);

        // Dependency injections
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('persist');
        $em->expects(self::never())->method('flush');
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())->method('validate')->willReturn($violations);

        // Assertions
        $this->expectException(PositionValidationException::class);

        // Start test
        $this->buildService(em: $em, validator: $validator)->create($input);
    }

    public function testCreatePositionWithAssetNotFoundThrowsException(): void
    {
        // Mock data
        $input = $this->createPositionInput(999, 1, '7410.86', '15.00', 'long');

        // Dependency injections
        $assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $assetRepository->expects(self::once())->method('find')->with(999)->willReturn(null);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())->method('validate')->willReturn(new ConstraintViolationList());

        // Assertions
        $this->expectException(AssetNotFoundException::class);

        // Start test
        $this->buildService(assetRepository: $assetRepository, validator: $validator)->create($input);
    }

    public function testCreatePositionWithTimeframeNotFoundThrowsException(): void
    {
        // Mock data
        $asset = $this->createAsset(1, 'SP500');
        $input = $this->createPositionInput(1, 999, '7410.86', '15.00', 'long');

        // Dependency injections
        $assetRepository = $this->createStub(AssetRepositoryInterface::class);
        $assetRepository->method('find')->willReturn($asset);
        $timeframeRepository = $this->createMock(TimeframeRepositoryInterface::class);
        $timeframeRepository->expects(self::once())->method('find')->with(999)->willReturn(null);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())->method('validate')->willReturn(new ConstraintViolationList());

        // Assertions
        $this->expectException(TimeframeNotFoundException::class);

        // Start test
        $this->buildService(
            assetRepository: $assetRepository,
            timeframeRepository: $timeframeRepository,
            validator: $validator
        )->create($input);
    }

    /* private helpers */

    private function buildService(
        ?PositionRepositoryInterface  $positionRepository  = null,
        ?AssetRepositoryInterface     $assetRepository     = null,
        ?TimeframeRepositoryInterface $timeframeRepository = null,
        ?OrderRepositoryInterface     $orderRepository     = null,
        ?EntityManagerInterface       $em                  = null,
        ?ValidatorInterface           $validator           = null,
    ): PositionService {
        return new PositionService(
            $positionRepository  ?? $this->createStub(PositionRepositoryInterface::class),
            $assetRepository     ?? $this->createStub(AssetRepositoryInterface::class),
            $timeframeRepository ?? $this->createStub(TimeframeRepositoryInterface::class),
            $orderRepository     ?? $this->createStub(OrderRepositoryInterface::class),
            $em                  ?? $this->createStub(EntityManagerInterface::class),
            $validator           ?? $this->createStub(ValidatorInterface::class),
        );
    }

    private function createPosition(int $id, string $direction, string $entryPrice, string $volume): Position
    {
        $position = new Position();
        $position->setId($id);
        $position->setDirection($direction);
        $position->setEntryPrice($entryPrice);
        $position->setVolume($volume);
        $position->setOpenedAt(new DateTimeImmutable('2026-06-08 15:32:00'));
        return $position;
    }

    private function createOrder(int $id): Order
    {
        $order = new Order();
        $order->setId($id);
        $order->setOrderType('limit');
        $order->setDirection('long');
        $order->setStatus('filled');
        $order->setSize('15.00');
        $order->setCreatedAt(new DateTimeImmutable('2026-06-08 15:30:00'));
        return $order;
    }

    private function createAsset(int $id, string $symbol): Asset
    {
        $asset = new Asset();
        $asset->setId($id);
        $asset->setSymbol($symbol);
        return $asset;
    }

    private function createTimeframe(int $id, string $label, int $seconds): Timeframe
    {
        $tf = new Timeframe();
        $tf->setId($id);
        $tf->setLabel($label);
        $tf->setSeconds($seconds);
        return $tf;
    }

    private function createPositionInput(
        int     $assetId,
        int     $timeframeId,
        string  $entryPrice,
        string  $volume,
        ?string $direction,
        ?int    $originOrderId = null
    ): PositionInput {
        $input                = new PositionInput();
        $input->assetId       = $assetId;
        $input->timeframeId   = $timeframeId;
        $input->openedAt      = '2026-06-08 15:32:00';
        $input->entryPrice    = $entryPrice;
        $input->volume        = $volume;
        $input->direction     = $direction;
        $input->originOrderId = $originOrderId;
        return $input;
    }
}
