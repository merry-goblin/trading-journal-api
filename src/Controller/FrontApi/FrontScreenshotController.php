<?php

namespace App\Controller\FrontApi;

use App\Domain\Service\Screenshot\ScreenshotStorage\ScreenshotStorageInterface;
use App\Repository\Screenshot\ScreenshotRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontScreenshotController extends AbstractController
{
    /**
     * Sert le fichier image d'un screenshot.
     * Protégé par JWT (firewall frontApi).
     * Le client Vue.js doit fetcher via Axios (avec Bearer token)
     * et créer un object URL pour l'affichage.
     */
    #[Route(
        '/frontApi/screenshot/{id}/image',
        name: 'front_screenshot_image',
        methods: ['GET'],
        requirements: ['id' => '\\d+']
    )]
    public function image(
        int $id,
        ScreenshotRepositoryInterface $screenshotRepository,
        ScreenshotStorageInterface $screenshotStorage
    ): Response {
        $screenshot = $screenshotRepository->find($id);

        if (!$screenshot) {
            return new JsonResponse(['error' => 'Not Found', 'status' => 404], 404);
        }

        $fullPath = $screenshotStorage->getFullPath($screenshot->getFilePath());

        if (!file_exists($fullPath)) {
            return new JsonResponse(
                ['error' => 'File not found', 'path' => $screenshot->getFilePath()],
                404
            );
        }

        $response = new BinaryFileResponse($fullPath);
        $response->headers->set('Content-Type', 'image/png');
        $response->headers->set('Cache-Control', 'private, max-age=3600');

        return $response;
    }
}
