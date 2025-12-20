<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use App\DTO\Asset\AssetInputMapperInterface;
use App\DTO\Asset\AssetOutputMapperInterface;
use App\Entity\Asset;
use App\Service\AssetServiceInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ApiAssetController extends AbstractController
{
    #[Route('/api/assets', name: 'listAssets', methods: ['GET'])]
    public function list(
        AssetServiceInterface $assetService,
        AssetOutputMapperInterface $outputMapper): JsonResponse
    {
        $assets = $assetService->list();

        // Response
        $output = array_map(fn(Asset $asset) => $outputMapper->fromEntity($asset), $assets);
        return $this->json($output);
    }

    #[Route('/api/asset/{id}', name: 'findAssetById', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(
        AssetServiceInterface $assetService,
        AssetOutputMapperInterface $outputMapper,
        int $id): JsonResponse
    {
        $asset = $assetService->get($id);
        if (!$asset) {
            return $this->json(['error' => 'Asset not found'], 404);
        }

        // Response
        $output = $outputMapper->fromEntity($asset);
        return $this->json($output);
    }

    #[Route('/api/asset/symbol/{symbol}', name: 'findAssetBySymbol', methods: ['GET'], requirements: ['symbol' => '[a-zA-Z_]\w+'])]
    public function showBySymbol(
        AssetServiceInterface $assetService,
        AssetOutputMapperInterface $outputMapper,
        string $symbol): JsonResponse
    {
        $asset = $assetService->getBySymbol($symbol);
        if (!$asset) {
            return $this->json(['error' => 'Asset not found'], 404);
        }

        // Response
        $output = $outputMapper->fromEntity($asset);
        return $this->json($output);
    }

    #[Route('/api/asset', name: 'createAsset', methods: ['POST'])]
    public function create(
        Request $request,
        AssetInputMapperInterface $inputMapper,
        AssetOutputMapperInterface $outputMapper,
        AssetServiceInterface $assetService,
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
        $asset = $assetService->create($input);

        // Response
        $output = $outputMapper->fromEntity($asset);
        return $this->json($output);
    }
}
