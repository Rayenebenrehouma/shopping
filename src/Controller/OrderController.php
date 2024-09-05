<?php

namespace App\Controller;

use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/commande/livraison', name: 'commande')]
    public function index(): Response
    {
        $form = $this->createForm(OrderType::class);

        return $this->render('order/index.html.twig',[
        'deliveryForm' => $form->createView(),
        ]);
    }
}
