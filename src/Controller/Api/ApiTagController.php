<?php

namespace App\Controller\Api;

use App\Repository\Tag\TagRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Expose les tags via l'API HMAC pour les EAs MT5.
 * Seul GET /api/tags est necessaire (lecture).
 */
final class ApiTagController extends AbstractController
{
    #[Route('/api/tags', name: 'api_tags_list', methods: ['GET'])]
    public function list(TagRepositoryInterface $tagRepository): JsonResponse
    {
        $tags = $tagRepository->findAll();
        return $this->json(array_map(
            fn($t) => [
                'id'    => $t->getId(),
                'label' => $t->getLabel(),
                'type'  => $t->getType(),
            ],
            $tags
        ));
    }
}
