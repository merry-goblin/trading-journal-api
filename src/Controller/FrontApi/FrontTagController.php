<?php

namespace App\Controller\FrontApi;

use App\DTO\FrontApi\Tag\TagInputMapperInterface;
use App\DTO\FrontApi\Tag\TagOutput;
use App\Domain\Service\Tag\TagServiceInterface;
use App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontTagController extends AbstractController
{
    private function toOutput(Tag $tag): TagOutput
    {
        $dto = new TagOutput();
        $dto->id = $tag->getId();
        $dto->label = $tag->getLabel();
        $dto->type = $tag->getType();
        $dto->description = $tag->getDescription() ?? '';
        return $dto;
    }

    #[Route('/frontApi/tags', name: 'front_tags', methods: ['GET'])]
    public function list(TagServiceInterface $service): JsonResponse
    {
        return $this->json(array_map(fn(Tag $t) => $this->toOutput($t), $service->list()));
    }

    #[Route('/frontApi/tag', name: 'front_tag_create', methods: ['POST'])]
    public function create(
        Request $request,
        TagInputMapperInterface $inputMapper,
        TagServiceInterface $service
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) return $this->json(['error' => 'Invalid JSON'], 400);
        $input = $inputMapper->fromArray($data);
        return $this->json($this->toOutput($service->create($input)), 201);
    }

    #[Route('/frontApi/tag/{id}', name: 'front_tag_delete', methods: ['DELETE'], requirements: ['id' => '\\d+'])]
    public function delete(TagServiceInterface $service, int $id): JsonResponse
    {
        $service->delete($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
