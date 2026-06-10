<?php

namespace App\Controller\FrontApi;

use App\Repository\Asset\AssetRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class FrontAssetController extends AbstractController
{
    #[Route('/frontApi/assets', name: 'front_assets', methods: ['GET'])]
    public function list(AssetRepositoryInterface $assetRepository): JsonResponse
    {
        $assets = $assetRepository->findAll();
        return $this->json(array_map(fn($a) => [
            'id'     => $a->getId(),
            'symbol' => $a->getSymbol(),
            'type'   => $a->getType(),
        ], $assets));
    }
}
