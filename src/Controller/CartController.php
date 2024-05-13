<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig',[
            'cart' => $cart->getCart()
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id, Cart $cart, ProductRepository $productRepository, Request $request): Response
    {
        $product = $productRepository->findOneById($id);

        $cart->add($product);

        $this->addFlash(
            'success',
            'Produit correctement ajouté à votre panier'
        );

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart/decrease/{id}', name: 'cart_decrease')]
    public function decrease($id, Cart $cart): Response
    {
        $cart->decrease($id);

        $this->addFlash(
            'success',
            'Produit correctement supprimée à votre panier'
        );

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove', name: 'cart_remove')]
    public function remove(Cart $cart): Response
    {

        $cart->remove();

        $this->addFlash(
            'danger',
            'Votre panier a bien été vidé'
        );

        return $this->redirectToRoute('home');
    }
}
