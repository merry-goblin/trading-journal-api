<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Product;
use App\DTO\Product\ProductInput;
use App\DTO\Product\ProductInputMapper;
use App\DTO\Product\ProductOutput;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ApiProductController extends AbstractController
{
    #[Route('/api/products', name: 'listProducts', methods: ['GET'])]
    public function list(
        ProductService $productService): JsonResponse
    {
        $products = $productService->list();

        return $this->json($products);
    }

    #[Route('/api/product/{id}', name: 'showProduct', methods: ['GET'])]
    public function show(
        ProductService $productService,
        int $id): JsonResponse
    {
        $product = $productService->get($id);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        return $this->json($product);
    }

    #[Route('/api/product', name: 'createProduct', methods: ['POST'])]
    public function create(
        Request $request,
        ProductInputMapper $inputMapper,
        ProductService $service,
        ValidatorInterface $validator): JsonResponse 
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }

        $input = $inputMapper->fromArray($data);

        $errors = $validator->validate($input);
        if (count($errors) > 0) {
            $list = [];
            foreach ($errors as $e) {
                $list[$e->getPropertyPath()] = $e->getMessage();
            }
            return $this->json([
                'error' => 'Validation failed',
                'details' => $errors,
            ], 422);
        }

        $output = $service->create($input);
        return $this->json($output);
    }
}
