<?php

namespace App\Controller;

use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\DTO\Screenshot\ScreenshotInputMapperInterface;
use App\DTO\Screenshot\ScreenshotOutputMapperInterface;
use App\Entity\Screenshot;
use App\Domain\Service\Screenshot\ScreenshotServiceInterface;

final class ApiScreenshotController extends AbstractController
{
    #[Route('/api/screenshots', name: 'listScreenshots', methods: ['GET'])]
    public function list(
        ScreenshotServiceInterface $screenshotService,
        ScreenshotOutputMapperInterface $outputMapper): JsonResponse
    {
        $screenshots = $screenshotService->list();

        // Response
        $output = array_map(fn(Screenshot $screenshot) => $outputMapper->fromEntity($screenshot), $screenshots);
        return $this->json($output);
    }

    #[Route('/api/screenshot/{id}', name: 'findScreenshotById', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(
        ScreenshotServiceInterface $screenshotService,
        ScreenshotOutputMapperInterface $outputMapper,
        int $id): JsonResponse
    {
        $screenshot = $screenshotService->get($id);

        // Response
        $output = $outputMapper->fromEntity($screenshot);
        return $this->json($output);
    }

    #[Route('/api/screenshot', name: 'createScreenshot', methods: ['POST'])]
    public function create(
        Request $request,
        ScreenshotInputMapperInterface $inputMapper,
        ScreenshotOutputMapperInterface $outputMapper,
        ScreenshotServiceInterface $screenshotService): JsonResponse
    {
        // File
        $file = $request->files->get('file');
        if (!$file instanceof UploadedFile) {
            return $this->json(['error' => 'File is required'], Response::HTTP_BAD_REQUEST);
        }
        if (!$file->isValid()) {
            return $this->json(['error' => 'File is invalid'], Response::HTTP_BAD_REQUEST);
        }

        // Input data
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }
        $input = $inputMapper->fromArray($data);

        // Entity creation
        $screenshot = $screenshotService->create(
            $input,
            new SplFileObject($file->getPathname()),
            $file->getClientMimeType()
        );

        // Response
        $output = $outputMapper->fromEntity($screenshot);
        return $this->json($output);
    }
}
