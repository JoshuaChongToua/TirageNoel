<?php

namespace App\Controller;

use App\Entity\Choix;
use App\Form\ChoixType;
use App\Repository\PartieRejointRepository;
use App\Repository\PartieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChoixController extends AbstractController
{
    #[Route('/choix', name: 'app_choix')]
    public function index(): Response
    {
        return $this->render('choix/index.html.twig', [
            'controller_name' => 'ChoixController',
        ]);
    }

    #[Route('/choix/choixPersonne/{id}', name: 'app_choixPersonne')]
    public function choixPersonne(Request $request, UserRepository $userRepository, PartieRejointRepository $partieRejointRepository, PartieRepository $partieRepository, $id, EntityManagerInterface $em): Response
    {
        $util = $this->getUser();
        $partie = $partieRepository->find($id);


        $form = $this->createForm(ChoixType::class, null, ['partie' => $partie]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $choix = new Choix();
            $choix->setJoueur($util); 
            $choix->setPersonneChoisie($form->get('personneChoisie')->getData());
            $choix->setPartie($partie);
            //dd($form->get('personneChoisie')->getData()->getId());
            
            $em->persist($choix);
            $em->flush();

            $this->addFlash('success', 'Votre choix a été enregistré.');

            return $this->redirectToRoute('mes_parties_view', ['id' => $partie->getId()]); 
        }

        return $this->render('choix/edit.html.twig', [
            'controller_name' => 'ChoixController',
            'form' => $form
        ]);
    }


}
