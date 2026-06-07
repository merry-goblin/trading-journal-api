<?php

namespace App\Domain\Service\Order;

use App\Domain\Exception\NotFoundException\AssetNotFoundException;
use App\Domain\Exception\NotFoundException\OrderNotFoundException;
use App\Domain\Exception\NotFoundException\TimeframeNotFoundException;
use App\Domain\Exception\ValidationException\OrderValidationException;
use App\DTO\Order\OrderInput;
use App\Entity\Order;
use App\Repository\Asset\AssetRepositoryInterface;
use App\Repository\Order\OrderRepositoryInterface;
use App\Repository\Timeframe\TimeframeRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private OrderRepositoryInterface     $repository,
        private AssetRepositoryInterface     $assetRepository,
        private TimeframeRepositoryInterface $timeframeRepository,
        private EntityManagerInterface       $em,
        private ValidatorInterface           $validator
    ) {}

    public function list(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @throws OrderNotFoundException
     */
    public function get(int $id): Order
    {
        $order = $this->repository->find($id);
        if (null === $order) {
            throw new OrderNotFoundException('Order not found');
        }
        return $order;
    }

    /**
     * @throws OrderValidationException
     * @throws AssetNotFoundException
     * @throws TimeframeNotFoundException
     */
    public function create(OrderInput $input): Order
    {
        $violations = $this->validator->validate($input);
        if (count($violations) > 0) {
            throw new OrderValidationException($violations);
        }

        $asset = $this->assetRepository->find($input->assetId);
        if (!$asset) { throw new AssetNotFoundException(); }

        $timeframe = $this->timeframeRepository->find($input->timeframeId);
        if (!$timeframe) { throw new TimeframeNotFoundException(); }

        $order = new Order();
        $order->setAsset($asset);
        $order->setTimeframe($timeframe);
        $order->setCreatedAt(new DateTimeImmutable($input->createdAt));
        $order->setOrderType($input->orderType);
        $order->setDirection($input->direction);
        $order->setPrice($input->price);
        $order->setStopPrice($input->stopPrice);
        $order->setSize($input->size);
        $order->setStopLoss($input->stopLoss);
        $order->setTakeProfit($input->takeProfit);
        $order->setStatus($input->status);
        $order->setComment($input->comment);

        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    /**
     * @throws OrderNotFoundException
     */
    public function updateStatus(int $id, string $status): Order
    {
        $order = $this->get($id);
        $order->setStatus($status);
        $this->em->flush();
        return $order;
    }
}
