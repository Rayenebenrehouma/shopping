<?php

namespace App\Controller;

use App\Entity\Carrier;
use App\Form\CarrierType;
use App\Repository\CarrierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/carrier')]
class CarrierController extends AbstractController
{
    #[Route('/', name: 'app_carrier_index', methods: ['GET'])]
    public function index(CarrierRepository $carrierRepository): Response
    {
        return $this->render('carrier/index.html.twig', [
            'carriers' => $carrierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_carrier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $carrier = new Carrier();
        $form = $this->createForm(CarrierType::class, $carrier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($carrier);
            $entityManager->flush();

            return $this->redirectToRoute('app_carrier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('carrier/new.html.twig', [
            'carrier' => $carrier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carrier_show', methods: ['GET'])]
    public function show(Carrier $carrier): Response
    {
        return $this->render('carrier/show.html.twig', [
            'carrier' => $carrier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_carrier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Carrier $carrier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarrierType::class, $carrier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_carrier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('carrier/edit.html.twig', [
            'carrier' => $carrier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carrier_delete', methods: ['POST'])]
    public function delete(Request $request, Carrier $carrier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$carrier->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($carrier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_carrier_index', [], Response::HTTP_SEE_OTHER);
    }
}
