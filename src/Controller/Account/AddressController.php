<?php

namespace App\Controller\Account;


use App\Classe\Cart;
use App\Entity\Adress;
use App\Form\AddressUserType;
use App\Repository\AdressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddressController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    //LISTE DES ADRESSES
    #[Route('/compte/adresses', name: 'account_addresses')]
    public function index(): Response
    {
        return $this->render('account/address/index.html.twig');
    }

    //SUPPRIMER UNE ADDRESSE
    #[Route('/compte/adress/delete/{id}', name: 'account_address_delete')]
    public function Delete($id, AdressRepository $adressRepository): Response
    {
        $address = $adressRepository->findOneById($id);
        if (!$address && $address->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_addresses');
        }
        $this->addFlash('success','Votre adresse est correctement supprimée.');
        $this->entityManager->remove($address);
        $this->entityManager->flush();
        return $this->redirectToRoute('account_addresses');
    }

    //AJOUTER UNE ADDRESSE
    #[Route('/compte/adresse/ajouter/{id}', name: 'account_address_form', defaults: ['id' => null])]
    public function Form(Request $request, $id, AdressRepository $adressRepository, Cart $cart): Response
    {
        if($id){
            $address = $adressRepository->findOneById($id);
            if (!$address && $address->getUser() != $this->getUser()){
                return $this->redirectToRoute('account_addresses');
            }
        }else{
            $address = new Adress();
            $address->setUser($this->getUser());
        }

        $form = $this->createForm(AddressUserType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($address);
            $this->entityManager->flush();

            $this->addFlash('success','Votre adresse est correctement sauvegardée.');

            if ($cart->fullQuantity() > 0){
                return $this->redirectToRoute('commande');
            }
            return $this->redirectToRoute('account_addresses');
        }

        return $this->render('account/address/form.html.twig', [
            'addressForm' => $form
        ]);
    }
}
?>