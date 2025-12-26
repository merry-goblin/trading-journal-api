<?php

namespace App\Service;

use App\Entity\Screenshot;
use App\Repository\ScreenshotRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\DTO\Screenshot\ScreenshotInput;
use DateTimeImmutable;
use DateTime;

class ScreenshotService
{
    public function __construct(
        private ScreenshotRepository $repository,
        private EntityManagerInterface $em
    ) {}

    public function list(): array
    {
        return $this->repository->findAll();
    }

    public function get(int $id): ?Screenshot
    {
        return $this->repository->find($id);
    }

    public function create(ScreenshotInput $input): Screenshot
    {
        $screenshot = new Screenshot();
        $screenshot->setFilePath($input->filePath);
        $screenshot->setCreatedAt(new DateTimeImmutable($input->createdAt));
        $screenshot->setDescription($input->description);
        $screenshot->setPeriodStart(new DateTime($input->periodStart));
        $screenshot->setPeriodEnd(new DateTime($input->periodEnd));
        $screenshot->setSource($input->source);

        $this->em->persist($screenshot);
        $this->em->flush();

        return $screenshot;
    }
}
