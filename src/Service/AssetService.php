<?php

namespace App\Service;

use App\Domain\Exception\AssetNotFoundException;
use App\DTO\Asset\AssetInput;
use App\Entity\Asset;
use App\Repository\AssetRepositoryInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

class AssetService implements AssetServiceInterface
{
    public function __construct(
        private AssetRepositoryInterface $repository,
        private EntityManagerInterface $em
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
