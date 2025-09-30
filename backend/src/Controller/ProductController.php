<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_product_list', methods: ['GET'])]
    public function list(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'externalId' => $product->getExternalId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'netPrice' => $product->getNetPrice(),
                'image' => $product->getImage(),
                'description' => $product->getDescription(),
                'category' => $product->getCategory()?->getName(),
                'type' => $product->getType(),
                'diameter' => $product->getDiameter(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/product/{id}', name: 'api_product_detail', methods: ['GET'])]
    public function detail(int $id, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        return $this->json([
            'id' => $product->getId(),
            'externalId' => $product->getExternalId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'netPrice' => $product->getNetPrice(),
            'image' => $product->getImage(),
            'description' => $product->getDescription(),
            'category' => $product->getCategory()?->getName(),
            'type' => $product->getType(),
            'diameter' => $product->getDiameter(),
        ]);
    }
}
