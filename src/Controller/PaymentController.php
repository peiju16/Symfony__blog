<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Repository\OrderDetailRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'app_payment')]
    public function index(Request $request, ProductRepository $productRepository, EntityManagerInterface $em, OrderRepository $orderRepository): Response
    {

        // je récupère la session cart
        $cart = $request->getSession()->get('cart');
        // je crée une commande
        $order = new Order();
        $carTotal = 0;

        for ($i=0; $i < count($cart["id"]); $i++) { 
           $carTotal += (float) $cart['price'][$i] * $cart["quantity"][$i];
        }

        $order->setAmount($carTotal);
        $order->setState('En cours');
        $order->setUser($this->getUser());
        $order->setDate(new\DateTime);        
        
        // pour chaque élément de mon panier je créé un détail de commande
        for ($i=0; $i < count($cart["id"]); $i++) { 
           $orderDetail = new OrderDetail;
           $orderDetail->setOrderNumber($orderRepository->findOneBy([],['id' => 'DESC']));
           $orderDetail->setProduct($productRepository->find($cart['id'][$i]));
           $orderDetail->setQuantity($cart["id"][$i]);

           $em->persist($orderDetail);
           $em->flush();
        }

        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
}
