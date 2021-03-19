<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * ProductService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param int $productId
     * @return Product
     */
    public function getProductById(int $productId): Product
    {
        /** @var Product $product */
        $product = $this->em->getRepository(Product::class)->find($productId);
        return $product;
    }

    /**
     * @param int $companyId
     * @return Product[]
     */
    public function getAllByCompany(int $companyId): array
    {
        /** @var Product[] $products */
        $products = $this->em->getRepository(Product::class)
                    ->findBy(array('company' => $companyId), array('commodity' => 'ASC'));
        return $products;
    }

    /**
     * @return Product[]
     */
    public function getAll(): array
    {
        /** @var Product[] $products */
        $products = $this->em->getRepository(Product::class)->findAll();
        return $products;
    }
}
