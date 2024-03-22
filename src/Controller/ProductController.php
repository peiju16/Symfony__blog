<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(EntityManagerInterface $em): Response
    {
        $products = $em->getRepository(Product::class)->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product, EntityManagerInterface $em): Response
    {
      
        return $this->render('product/show.html.twig', [
            'product' => $product,
        
           
        ]);
    }

}
