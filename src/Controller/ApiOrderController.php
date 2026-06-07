<?php

namespace App\Controller;

use App\DTO\Order\OrderInputMapperInterface;
use App\DTO\Order\OrderOutputMapperInterface;
use App\Domain\Service\Order\OrderServiceInterface;
use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ApiOrderController extends AbstractController
{
    #[Route('/api/orders', name: 'listOrders', methods: ['GET'])]
    public function list(
        OrderServiceInterface    $orderService,
        OrderOutputMapperInterface $outputMapper
    ): JsonResponse {
        $orders = $orderService->list();
        $output = array_map(fn(Order $o) => $outputMapper->fromEntity($o), $orders);
        return $this->json($output);
    }

    #[Route('/api/order/{id}', name: 'findOrderById', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(
        OrderServiceInterface    $orderService,
        OrderOutputMapperInterface $outputMapper,
        int $id
    ): JsonResponse {
        $order  = $orderService->get($id);
        $output = $outputMapper->fromEntity($order);
        return $this->json($output);
    }

    #[Route('/api/order', name: 'createOrder', methods: ['POST'])]
    public function create(
        Request                   $request,
        OrderInputMapperInterface  $inputMapper,
        OrderOutputMapperInterface $outputMapper,
        OrderServiceInterface     $orderService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }
        $input  = $inputMapper->fromArray($data);
        $order  = $orderService->create($input);
        $output = $outputMapper->fromEntity($order);
        return $this->json($output, 201);
    }

    #[Route('/api/order/{id}/status', name: 'updateOrderStatus', methods: ['PATCH'], requirements: ['id' => '\\d+'])]
    public function updateStatus(
        Request                   $request,
        OrderOutputMapperInterface $outputMapper,
        OrderServiceInterface     $orderService,
        int $id
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (empty($data['status'])) {
            return $this->json(['error' => 'Missing status field'], 400);
        }
        $order  = $orderService->updateStatus($id, (string) $data['status']);
        $output = $outputMapper->fromEntity($order);
        return $this->json($output);
    }
}
