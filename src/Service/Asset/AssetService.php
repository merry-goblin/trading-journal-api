<?php

namespace App\Service\Asset;

use App\Domain\Exception\NotFoundException\AssetNotFoundException;
use App\Domain\Exception\ValidationException\AssetValidationException;
use App\DTO\Asset\AssetInput;
use App\Entity\Asset;
use App\Repository\Asset\AssetRepositoryInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AssetService implements AssetServiceInterface
{
    public function __construct(
        private AssetRepositoryInterface $repository,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    ) {}

    public function list(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @throws AssetNotFoundException
     */
    public function get(int $id): Asset
    {
        $asset = $this->repository->find($id);
        if (null === $asset) {
            throw new AssetNotFoundException('Asset not found');
        }
        return $asset;
    }

    /**
     * @throws AssetNotFoundException
     */
    public function getBySymbol(string $symbol): Asset
    {
        $asset = $this->repository->findOneBy(['symbol' => $symbol]);
        if (null === $asset) {
            throw new AssetNotFoundException('Asset not found');
        }
        return $asset;
    }

    /**
     * @throws SymbolAlreadyExistsException
     */
    public function create(AssetInput $input): Asset
    {
        // Validation
        $violations = $this->validator->validate($input);
        if (count($violations) > 0) {
            throw new AssetValidationException($violations);
        }

        $asset = new Asset();
        $asset->setSymbol($input->symbol);
        $asset->setType($input->type);
        $asset->setDescription($input->description);

        try {
            $this->em->persist($asset);
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new SymbolAlreadyExistsException($input->symbol.' symbol already exists');
        }

        return $asset;
    }
}
