<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\DTO\Product\ProductInput;

class ProductService
{
    public function __construct(
        private ProductRepository $repository,
        private EntityManagerInterface $em,
    ) {}

    public function list(): array
    {
        return $this->repository->findAll();
    }

    public function get(int $id): ?Product
    {
        return $this->repository->find($id);
    }

    public function create(ProductInput $input): Product
    {
        $product = new Product();
        $product->setName($input->name);
        $product->setPrice($input->price);
        $product->setDescription($input->description);

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }
}
