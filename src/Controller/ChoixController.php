<?php

namespace App\Controller;

use App\Entity\Choix;
use App\Form\ChoixType;
use App\Repository\ChoixRepository;
use App\Repository\PartieRejointRepository;
use App\Repository\PartieRepository;
use App\Repository\TirageResultatRepository;
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

    #[Route('/choix/choixPersonne/{id}/{idUser}', name: 'app_choixPersonne')]
    public function choixPersonne(
        TirageResultatRepository $tirageResultatRepository,
        $idUser,
        ChoixRepository $choixRepository,
        Request $request,
        UserRepository $userRepository,
        PartieRejointRepository $partieRejointRepository,
        PartieRepository $partieRepository,
        $id,
        EntityManagerInterface $em
    ): Response {

        $user = $userRepository->find($idUser);
        $partie = $partieRepository->find($id);
        $tirageResultatRepo = $tirageResultatRepository->findBy(['partie' => $partie]);

        $form = $this->createForm(ChoixType::class, null, ['partie' => $partie]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ancienChoix = $choixRepository->findOneBy(['joueur' => $user, 'partie' => $partie]);
            if ($ancienChoix) {
                $em->remove($ancienChoix);
            }

            $choix = new Choix();
            $choix->setJoueur($user);
            $choix->setPersonneChoisie($form->get('personneChoisie')->getData());
            $choix->setPartie($partie);
            $em->persist($choix);

            foreach ($tirageResultatRepo as $tirage) {
                if ($tirage->getJoueur()->getId() == $user->getId()) {
                    $tirage->setDestinataire($choix->getPersonneChoisie());
                    $em->persist($tirage);
                }
            }

            $em->flush();

            $this->addFlash('success', 'Votre choix a été enregistré.');

            return $this->redirectToRoute('mes_parties_view', ['id' => $partie->getId()]);
        }

        // Affichage du formulaire
        return $this->render('choix/edit.html.twig', [
            'controller_name' => 'ChoixController',
            'form' => $form
        ]);
    }




}
