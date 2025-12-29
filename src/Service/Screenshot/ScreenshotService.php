<?php

namespace App\Service\Screenshot;

use App\Domain\Exception\NotFoundException\ScreenshotNotFoundException;
use App\Domain\Exception\ValidationException\ScreenshotValidationException;
use App\DTO\Screenshot\ScreenshotInput;
use App\Entity\Screenshot;
use App\Repository\Screenshot\ScreenshotRepositoryInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use DateTimeImmutable;
use DateTime;

class ScreenshotService implements ScreenshotServiceInterface
{
    public function __construct(
        private ScreenshotRepositoryInterface $repository,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    ) {}

    public function list(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @throws ScreenshotNotFoundException
     */
    public function get(int $id): Screenshot
    {
        $screenshot = $this->repository->find($id);
        if (null === $screenshot) {
            throw new ScreenshotNotFoundException('Screenshot not found');
        }
        return $screenshot;
    }

    /**
     * @throws FilePathAlreadyExistsException
     */
    public function create(ScreenshotInput $input): Screenshot
    {
        // Validation
        $violations = $this->validator->validate($input);
        if (count($violations) > 0) {
            throw new ScreenshotValidationException($violations);
        }

        $screenshot = new Screenshot();
        $screenshot->setFilePath($input->filePath);
        $screenshot->setCreatedAt(new DateTimeImmutable($input->createdAt));
        $screenshot->setDescription($input->description);
        $screenshot->setPeriodStart(new DateTime($input->periodStart));
        $screenshot->setPeriodEnd(new DateTime($input->periodEnd));
        $screenshot->setSource($input->source);

        try {
            $this->em->persist($screenshot);
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new FilePathAlreadyExistsException($input->filePath.' file path already exists');
        }

        return $screenshot;
    }
}
