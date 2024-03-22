<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(EntityManagerInterface $em): Response
    {


        return $this->render('cart/index.html.twig', [
            'cartItems' => [],
            'cartTotal' => 100,
        ]);
    }

    #[Route('/cart/{idProduct}', name: 'app_cart_add', methods: ['GET','POST'])]
    public function addProduct(ProductRepository $productRepository, Request $request, int $idProduct): Response
    {

         // créer la session

        $session= $request->getSession();

        if (!$session->get('cart')) {
            $session->set('cart', [
                "id" => [],
                "name" => [],
                "description" => [],
                "picture" => [],
                "price" => [],
                "quantity" => [],
            ]);
        }

        $cart = $session->get('cart');
     

        // ajouter le produit au panier

        // récupérer les infos du produit en BDD et l'ajouter à mon panier
        $product = $productRepository->find($idProduct);
        $cart["id"][] = $product->getId();
        $cart["name"][] = $product->getName();
        $cart["description"][] = $product->getDescription();
        $cart["picture"][] = $product->getPicture();
        $cart["price"][] = $product->getPrice();
        $cart["quantity"][] = 1;

        $session->set('cart', $cart);

        // calculer le montant total de mon panier
        $carTotal = 0;

        for ($i=0; $i < count($session->get('cart')["id"]); $i++) { 
           $carTotal += floatval($session->get('cart')['id'][$i]) * $session->get('cart')["quantity"][$i];
        }

        // afficher la page panier

        return $this->render('cart/index.html.twig', [
            'cartItems' => $session->get('cart'),
            'cartTotal' => $carTotal,
        ]);
    }
}

