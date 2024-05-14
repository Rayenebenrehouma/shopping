<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    public function __construct(private RequestStack $requestStack)
    {

    }

    /*
     * Fonction permettant l'ajout au panier
     *
     */
    public function add($product)
    {
        $session = $this->requestStack->getSession();
        $cart = $this->getCart();

        if (isset($cart[$product->getId()])){
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => $cart[$product->getId()]['qty'] + 1
            ];
        }else{
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => 1
            ];
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }

    /*
     * Fonction permettant la suppression d'un produit du panier
     */
    public function decrease($id)
    {
        $cart = $this->getCart();

        if($cart[$id]['qty'] > 1) {
            $cart[$id]['qty'] = $cart[$id]['qty'] -1;
        }else{
            unset($cart[$id]);
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }

    /*
     * Fonction retournant la quantité total des produit
     */
    public function fullQuantity()
    {
        $cart = $this->getCart();
        $quantity = 0;

        if(!isset($cart)){
            return $quantity;
        }
        foreach ($cart as $product){
            $quantity = $quantity + $product['qty'];
        }
        return $quantity;
    }

    /*
     * Fonction retournant le prix total sans taxe
     */
    public function getTotalWt()
    {
        $cart = $this->getCart();
        $price = 0;

        if (!isset($cart)){
            return $price;
        }
        foreach ($cart as $product){
            $price = $price + ($product['object']->getPriceWt() * $product['qty']);
        }

       return $price;
    }

    /*
     * Fonction vidant totalement le panier
     */
    public function remove()
    {
        return $this->requestStack->getSession()->remove('cart');
    }

    /*
     * Fonction récupérant le panier
     */
    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart');
    }
}