<?php

namespace App\Controller;

use App\Entity\Partie;
use App\Entity\Restriction;
use App\Form\RestrictionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RestrictionController extends AbstractController
{
    #[Route('/restriction', name: 'app_restriction')]
    public function index(): Response
    {
        return $this->render('restriction/index.html.twig', [
            'controller_name' => 'RestrictionController',
        ]);
    }

    #[Route('/mes-partie/{id}/restriction', name: 'partie_restriction')]
    public function addRestriction(Partie $partie, Request $request, EntityManagerInterface $em): Response
    {
        $restriction = new Restriction();
        $restriction->setPartie($partie);

        $form = $this->createForm(RestrictionType::class, $restriction, ['partie'=>$partie]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $restriction = $form->getData();
            $em->persist($restriction);
            $em->flush();

            $this->addFlash('success', 'Restriction ajoutée avec succès.');
            return $this->redirectToRoute('mes_parties_view', ['id' => $partie->getId()]);
        }

        // Affiche le formulaire
        return $this->render('restrictions/edit.html.twig', [
            'form' => $form->createView(),
            'partie' => $partie,
        ]);
    }

    
}
