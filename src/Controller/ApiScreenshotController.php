<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use App\DTO\Screenshot\ScreenshotInputMapper;
use App\DTO\Screenshot\ScreenshotOutputMapper;
use App\Service\ScreenshotService;

use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ApiScreenshotController extends AbstractController
{
    /*#[Route('/api/{symbol}/screenshot', name: 'listScreenshots', methods: ['GET'])]
    public function list(
        ProductService $productService): JsonResponse
    {
        $products = $productService->list();

        return $this->json($products);
    }*/

    #[Route('/api/screenshot/{id}', name: 'showScreenshot', methods: ['GET'])]
    public function show(
        ScreenshotService $screenshotService,
        int $id): JsonResponse
    {
        $screenshot = $screenshotService->get($id);
        if (!$screenshot) {
            return $this->json(['error' => 'Screenshot not found'], 404);
        }

        return $this->json($screenshot);
    }

    #[Route('/api/screenshot', name: 'createScreenshot', methods: ['POST'])]
    public function create(
        Request $request,
        ScreenshotInputMapper $inputMapper,
        ScreenshotOutputMapper $outputMapper,
        ScreenshotService $screenshotService,
        ValidatorInterface $validator): JsonResponse 
    {
        // input data
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }
        $input = $inputMapper->fromArray($data);

        // validation
        $errors = $validator->validate($input);
        if (count($errors) > 0) {
            $list = [];
            foreach ($errors as $e) {
                $list[$e->getPropertyPath()] = $e->getMessage();
            }
            return $this->json([
                'error' => 'Validation failed',
                'details' => $errors
            ], 422);
        }

        // entity creation
        $screenshot = $screenshotService->create($input);

        // output data
        $output = $outputMapper->fromEntity($screenshot);
        return $this->json($output);
    }
}
