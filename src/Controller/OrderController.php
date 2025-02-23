<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    /**
     * @return 1ère étape du tunnel d'achat
     * choix de l'adresse de livraison et du transporteur
     */
    #[Route('/commande/livraison', name: 'commande')]
    public function index(): Response
    {
        $addresses = $this->getUser()->getAdresses();

        if(count($addresses) == 0){
            return $this->redirectToRoute('account_address_form');
        }

        $form = $this->createForm(OrderType::class, null, [
            'addresses' =>  $addresses,
            'action'    => $this->generateUrl('summary')
        ]);



        return $this->render('order/index.html.twig',[
        'deliveryForm' => $form->createView(),
        ]);
    }

    /**
     * @return 2ème étape du tunnel d'achat
     * Récapitulatif de l'adresse de livraison et du transporteur
     * Insertion en bdd
     * Préparation du paiement avec Stripe
     */
    #[Route('/commande/récapitulatif', name: 'summary')]
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() !== 'POST') {
            return $this->redirectToRoute('cart');
        }

        $products = $cart->getCart();

        $form = $this->createForm(OrderType::class, null, [
            'addresses' =>  $this->getUser()->getAdresses(),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $addressObj = $form->get('address')->getData();

            $address = $addressObj->getFirstName() . " " . $addressObj->getLastName() .'<br/>';
            $address .= $addressObj->getAdress() .'<br/>';
            $address .= $addressObj->getCity() ." " . $addressObj->getPostal() . " " . $addressObj->getCity() .'<br/>';
            $address .= $addressObj->getCountry() . '<br/>';
            $address .= $addressObj->getPhone() . '<br/>';



            $order = new Order();
            $user = $this->getUser();
            $order->setUser($user);
            $order->setCreatedAt(new \DateTime());
            $order->setCarrierName($form->get('carrier')->getData()->getName());
            $order->setCarrierPrice($form->get('carrier')->getData()->getPrice());
            $order->setAddress($form->get('address')->getData());
            $order->setDelivery($address);
            $order->setState(1);

            foreach ($products as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setProductName($product['object']->getName());
                $orderDetails->setProductIllustration($product['object']->getIllustration());
                $orderDetails->setProductPrice($product['object']->getPrice());
                $orderDetails->setProductQuantity($product['qty']);
                $orderDetails->setProductTva($product['object']->getTva());
                $order->addOrderDetail($orderDetails);
            }
            //****************ARRETE ICI************************
            $entityManager->persist($order);
            $entityManager->flush();
        }

        return $this->render('order/summary.html.twig',[
            'choices' => $form->getData(),
            'cart' => $products,
            'totalWt' => $cart->getTotalWt()
        ]);
    }
}