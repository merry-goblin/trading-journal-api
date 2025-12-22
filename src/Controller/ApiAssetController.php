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

        // Response
        $output = $outputMapper->fromEntity($asset);
        return $this->json($output);
    }

    #[Route('/api/asset', name: 'createAsset', methods: ['POST'])]
    public function create(
        Request $request,
        AssetInputMapperInterface $inputMapper,
        AssetOutputMapperInterface $outputMapper,
        AssetServiceInterface $assetService): JsonResponse
    {
        // Input data
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }
        $input = $inputMapper->fromArray($data);

        // Entity creation
        $asset = $assetService->create($input);

        // Response
        $output = $outputMapper->fromEntity($asset);
        return $this->json($output);
    }
}
