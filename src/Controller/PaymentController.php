<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Repository\OrderDetailRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;

class PaymentController extends AbstractController
{
    
    #[Route('/payment', name: 'app_payment')]
    public function index(Request $request, ProductRepository $productRepository, EntityManagerInterface $em, OrderRepository $orderRepository, Security $security): Response
    {

        if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
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
        
        $em->persist($order);
        $em->flush();

        
        // pour chaque élément de mon panier je créé un détail de commande
        for ($i=0; $i < count($cart["id"]); $i++) { 
           $orderDetail = new OrderDetail;
           $orderDetail->setOrderNumber($orderRepository->findOneBy([],['id' => 'DESC']));
           $orderDetail->setProduct($productRepository->find($cart['id'][$i]));
           $orderDetail->setQuantity($cart["id"][$i]);

           $em->persist($orderDetail);
           $em->flush();

           

                // on génera le PDF
                // on l'enverra par mail la facture
                // on affichera une page de succès

          return $this->redirectToRoute("sucess");


        }

    }

        $session = $request->getSession();
        $session->set('url_retour', $request->getUri());

        // si pas connecté
        return $this->redirectToRoute('app_login');
    }

    #[Route('/success', name: 'success')]
    public function success(MailerInterface $mailer): Response
    {
        // le numéro de la dernière facture pour le user
        // le montant total
        // les produits achetés
        // => récupérer la dernière facture insérée en bdd pour le user
        // et tous les orderDetails liés à cette facture


        // on génera le PDF
        $pdfOptions = new Options();
        $pdfOptions->set(['defaultFont' => 'Arial', 'enable_remote' => true]);
        // 2- On crée le pdf avec les options
        $dompdf = new Dompdf($pdfOptions);

        // 3- On prépare le twig qui sera transformée en pdf
        $html = $this->renderView('invoice/index.html.twig', [
            'Amount' => 10,
            'invoiceNumber' => 'F1093',
            'date' => new \DateTime(),
            'products' => []
        ]);

        // 4- On transforme le twig en pdf avec les options de format
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        // 5- On enregistre le pdf dans une variable
        $dompdf->render();
        $finalInvoice = $dompdf->output();

        if (!file_exists('uploads/facture')) {
            mkdir('uploads/factures');
        }

        $invoiceNumber = 5;
        $pathInvoice = "./uploads/factures/" . $invoiceNumber . "_" . $this->getUser()->getId() . ".pdf";
        file_put_contents($pathInvoice, $finalInvoice);
        // on l'enverra par mail la facture
        // on affichera une page de succès

        $email = (new TemplatedEmail())
            ->from($this->getParameter('app.mailAddress'))
            ->to($this->getUser()->getEmail())
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject("Facture Blog Afpa 2024")
            // ->html('<p> ' . $contact->getMessage() . ' </p>');
            ->htmlTemplate("invoice/email.html.twig")
            ->attach($finalInvoice, sprintf('facture-' . $invoiceNumber . 'blog-afpa.pdf', date("Y-m-d")));

            $mailer->send($email);
    
            return $this->render("payment/success.html.twig", [
                'invoiceNumber' => $invoiceNumber,
                'amount' => 100,
            ]);

       
    }
}
