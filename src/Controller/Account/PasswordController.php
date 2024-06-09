<?php

namespace App\Controller\Account;

use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class PasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/modifier-pwd', name: 'modify-pwd')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->addFlash("success", "Le mot de passe a été modifié avec succès");
            $this->entityManager->flush();

            $this->redirectToRoute('account');
        }

        return $this->render('account/password/index.html.twig',[
            'modifyPwd' => $form->createView()
        ]);
    }
}
?>