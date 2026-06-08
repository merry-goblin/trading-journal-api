<?php

namespace App\Tests\Unit\Domain\Service\Order;

use App\Domain\Exception\NotFoundException\AssetNotFoundException;
use App\Domain\Exception\NotFoundException\OrderNotFoundException;
use App\Domain\Exception\NotFoundException\TimeframeNotFoundException;
use App\Domain\Exception\ValidationException\OrderValidationException;
use App\Domain\Service\Order\OrderService;
use App\DTO\Order\OrderInput;
use App\Entity\Asset;
use App\Entity\Order;
use App\Entity\Timeframe;
use App\Repository\Asset\AssetRepositoryInterface;
use App\Repository\Order\OrderRepositoryInterface;
use App\Repository\Timeframe\TimeframeRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderServiceTest extends TestCase
{
    /* get method */

    public function testGetOneOrderById(): void
    {
        // Mock data
        $expected = $this->createOrder(1, 'limit', 'long', 'pending');

        // Dependency injections
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($expected)
        ;

        // Start test
        $service = $this->buildService(orderRepository: $orderRepository);
        $order   = $service->get(1);

        // Assertions
        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame($expected, $order);
    }

    public function testGetOneOrderByIdNotFound(): void
    {
        // Dependency injections
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->expects(self::once())
            ->method('find')
            ->with(9999)
            ->willReturn(null)
        ;

        // Assertions
        $this->expectException(OrderNotFoundException::class);
        $this->expectExceptionMessage('Order not found');

        // Start test
        $this->buildService(orderRepository: $orderRepository)->get(9999);
    }

    /* list method */

    public function testListWithMultipleOrders(): void
    {
        // Mock data
        $expectedList = [
            $this->createOrder(1, 'limit', 'long',  'filled'),
            $this->createOrder(2, 'stop',  'short', 'cancelled'),
        ];

        // Dependency injections
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($expectedList)
        ;

        // Start test
        $list = $this->buildService(orderRepository: $orderRepository)->list();

        // Assertions
        $this->assertIsArray($list);
        $this->assertCount(2, $list);
        $this->assertContainsOnlyInstancesOf(Order::class, $list);
    }

    public function testListWithNoOrders(): void
    {
        // Dependency injections
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->expects(self::once())
            ->method('findAll')
            ->willReturn([])
        ;

        // Start test
        $list = $this->buildService(orderRepository: $orderRepository)->list();

        // Assertions
        $this->assertIsArray($list);
        $this->assertCount(0, $list);
    }

    /* create method */

    public function testCreateOrder(): void
    {
        // Mock data
        $asset     = $this->createAsset(1, 'SP500');
        $timeframe = $this->createTimeframe(1, 'M5', 300);
        $input     = $this->createOrderInput(1, 1, 'limit', 'long', '7410.86', '15.00', '7359.90', '7499.10', 'pending');

        // Dependency injections
        $orderRepository     = $this->createStub(OrderRepositoryInterface::class);
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
        $service = $this->buildService(
            orderRepository: $orderRepository,
            assetRepository: $assetRepository,
            timeframeRepository: $timeframeRepository,
            em: $em,
            validator: $validator
        );
        $order = $service->create($input);

        // Assertions
        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame($asset,     $order->getAsset());
        $this->assertSame($timeframe, $order->getTimeframe());
        $this->assertSame('limit',    $order->getOrderType());
        $this->assertSame('long',     $order->getDirection());
        $this->assertSame('7410.86',  $order->getPrice());
        $this->assertSame('pending',  $order->getStatus());
    }

    public function testCreateOrderWithInvalidPayloadThrowsException(): void
    {
        // Mock data
        $input      = $this->createOrderInput(0, 0, '', '', null, '', null, null, '');
        $violations = new ConstraintViolationList([
            new ConstraintViolation('This value should not be blank.', null, [], $input, 'orderType', ''),
        ]);

        // Dependency injections
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('persist');
        $em->expects(self::never())->method('flush');
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())->method('validate')->willReturn($violations);

        // Assertions
        $this->expectException(OrderValidationException::class);

        // Start test
        $this->buildService(em: $em, validator: $validator)->create($input);
    }

    public function testCreateOrderWithAssetNotFoundThrowsException(): void
    {
        // Mock data
        $input = $this->createOrderInput(999, 1, 'limit', 'long', '7410.86', '15.00', null, null, 'pending');

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

    public function testCreateOrderWithTimeframeNotFoundThrowsException(): void
    {
        // Mock data
        $asset = $this->createAsset(1, 'SP500');
        $input = $this->createOrderInput(1, 999, 'limit', 'long', '7410.86', '15.00', null, null, 'pending');

        // Dependency injections
        $assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $assetRepository->expects(self::once())->method('find')->willReturn($asset);
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

    /* updateStatus method */

    public function testUpdateStatusChangesOrderStatus(): void
    {
        // Mock data
        $order = $this->createOrder(1, 'limit', 'long', 'pending');

        // Dependency injections
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->expects(self::once())->method('find')->with(1)->willReturn($order);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        // Start test
        $updated = $this->buildService(orderRepository: $orderRepository, em: $em)->updateStatus(1, 'filled');

        // Assertions
        $this->assertSame('filled', $updated->getStatus());
    }

    public function testUpdateStatusWithOrderNotFoundThrowsException(): void
    {
        // Dependency injections
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->expects(self::once())->method('find')->with(9999)->willReturn(null);

        // Assertions
        $this->expectException(OrderNotFoundException::class);

        // Start test
        $this->buildService(orderRepository: $orderRepository)->updateStatus(9999, 'filled');
    }

    /* private helpers */

    private function buildService(
        ?OrderRepositoryInterface     $orderRepository     = null,
        ?AssetRepositoryInterface     $assetRepository     = null,
        ?TimeframeRepositoryInterface $timeframeRepository = null,
        ?EntityManagerInterface       $em                  = null,
        ?ValidatorInterface           $validator           = null,
    ): OrderService {
        return new OrderService(
            $orderRepository     ?? $this->createStub(OrderRepositoryInterface::class),
            $assetRepository     ?? $this->createStub(AssetRepositoryInterface::class),
            $timeframeRepository ?? $this->createStub(TimeframeRepositoryInterface::class),
            $em                  ?? $this->createStub(EntityManagerInterface::class),
            $validator           ?? $this->createStub(ValidatorInterface::class),
        );
    }

    private function createOrder(int $id, string $orderType, string $direction, string $status): Order
    {
        $order = new Order();
        $order->setId($id);
        $order->setOrderType($orderType);
        $order->setDirection($direction);
        $order->setStatus($status);
        $order->setSize('1.00');
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

    private function createOrderInput(
        int     $assetId,
        int     $timeframeId,
        string  $orderType,
        string  $direction,
        ?string $price,
        string  $size,
        ?string $stopLoss,
        ?string $takeProfit,
        string  $status
    ): OrderInput {
        $input              = new OrderInput();
        $input->assetId     = $assetId;
        $input->timeframeId = $timeframeId;
        $input->createdAt   = '2026-06-08 15:30:00';
        $input->orderType   = $orderType;
        $input->direction   = $direction;
        $input->price       = $price;
        $input->size        = $size;
        $input->stopLoss    = $stopLoss;
        $input->takeProfit  = $takeProfit;
        $input->status      = $status;
        return $input;
    }
}
