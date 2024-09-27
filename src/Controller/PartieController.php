<?php

namespace App\Controller;

use App\Entity\Partie;
use App\Entity\PartieCreate;
use App\Entity\PartieRejoint;
use App\Entity\Voiture;
use App\Form\PartieType;
use App\Form\RejoindrePartieType;
use App\Repository\PartieRejointRepository;
use App\Repository\PartieRepository;
use App\Repository\TirageResultatRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class PartieController extends AbstractController
{
    #[Route('/partie', name: 'app_partie')]
    #[IsGranted('ROLE_USER')]

    public function index(TirageResultatRepository $tirageResultatRepository, PartieRepository $partieRepository, PartieRejointRepository $partieRejointRepository): Response
    {
        $userId = $this->getUser()->getId();
        $partiesRejoints = $partieRejointRepository->findByUserId($userId);
        //dd($partiesRejoints);
        $parties = $partieRepository->findAll();

        $tirages = $tirageResultatRepository->findAll();

        return $this->render('partie/index.html.twig', [
            'controller_name' => 'PartieController',
            'parties' => $parties,
            'userId' => $userId,
            'partiesRejoints' => $partiesRejoints,
            'tirages' => $tirages
        ]);
    }

    #[Route('/parties/view/{id}/', name: 'parties_view')]
    public function view(PartieRejointRepository $partieRejointRepository, PartieRepository $partieRepo, $id, UserRepository $userRepository): Response
    {
        $partie = $partieRepo->find($id);
        $users = $userRepository->findAll();
        $partiesRejoints = $partieRejointRepository->findByPartieId($partie->getId()); // Supposons que vous ayez une relation

        return $this->render('partie/view.html.twig', [
            'partie' => $partie,
            'partiesRejoints' => $partiesRejoints,
            'users' => $users,
        ]);
    }

    #[Route('/partie/create', name: 'partie_create')]
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepository, UserPasswordHasherInterface $hasher): Response
    {
        $partie = new Partie();
        $partieCreate = new PartieCreate();
        $partieRejoint = new PartieRejoint();
        $userInterface = $this->getUser();
        $user = $userRepository->find($userInterface->getId());

        $form = $this->createForm(PartieType::class, $partie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $partie->getPassword();
            if (!empty($plainPassword)) {
                $partie->setPassword($hasher->hashPassword($user, $partie->getPassword())); 
            }
            $partie->setCreateur($this->getUser());
            $partieRejoint->setRole("hote");
            $partieRejoint->setUser($user);
            $partieRejoint->setPartie($partie);
            $partieCreate->setUser($user);
            $partieCreate->setPartie($partie);

            $em->persist($partie);
            $em->persist($partieCreate);
            $em->persist($partieRejoint);
            $em->flush();

            $this->addFlash('success', 'Creation Réussie');

            return $this->redirectToRoute('app_partie');
        }

        return $this->render('partie/create.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/partie/{id}/edit', name: 'partie_edit')]
    public function edit(Partie $partie, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {

        $originalPassword = $partie->getPassword(); 
        $user = $this->getUser();
        $form = $this->createForm(PartieType::class, $partie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('no_password')->getData()) {
                $partie->setPassword(null); // Définir le mot de passe à null si la case est cochée
            } else {
                $plainPassword = $partie->getPassword();
                if (!empty($plainPassword)) {
                    // Hacher le mot de passe s'il est renseigné
                    $partie->setPassword($hasher->hashPassword($user, $partie->getPassword()));
                }
            }
            $em->flush();
            $this->addFlash('success', 'Modification Réussie');

            return $this->redirectToRoute('app_partie');
        }

        return $this->render('partie/edit.html.twig', [
            'partie' => $partie,
            'form' => $form
        ]);
    }

    #[Route('/partie/{id}', name: 'partie_delete', methods: ['DELETE'])]
    public function remove(Partie $partie, EntityManagerInterface $em)
    {
        $em->remove($partie);
        $em->flush();
        $this->addFlash('success', 'Suppression Réussie');

        return $this->redirectToRoute('app_partie');
    }

    #[Route('/partie/rejoindre/{id}', name: 'partie_rejoindre')]
    public function rejoindre(Request $request, EntityManagerInterface $em, UserRepository $userRepository, PartieRepository $partieRepository): Response
    {
        $partie = $partieRepository->find($request->attributes->get('id'));

        if ($partie->getPassword() !== null) {
            $form = $this->createForm(RejoindrePartieType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                // Vérifiez le mot de passe
                if ($data['mot_de_passe'] !== $partie->getPassword()) {
                    $this->addFlash('error', 'Mot de passe incorrect.');

                    return $this->redirectToRoute('partie_rejoindre', ['id' => $request->attributes->get('id')]);
                }

                $userInterface = $this->getUser();
                $user = $userRepository->find($userInterface->getId());

                $partieRejoint = new PartieRejoint();
                $partieRejoint->setUser($user);
                $partieRejoint->setPartie($partie);
                $partieRejoint->setRole("joueur");

                $em->persist($partieRejoint);
                $em->flush();

                $this->addFlash('success', 'Vous avez rejoint la partie');

                return $this->redirectToRoute('app_partie');
            }

            return $this->render('partie/rejoindre.html.twig', [
                'form' => $form,
                'partie' => $partie, 
            ]);
        }

        // Si la partie ne nécessite pas de mot de passe, rejoignez directement
        $userInterface = $this->getUser();
        $user = $userRepository->find($userInterface->getId());

        $partieRejoint = new PartieRejoint();
        $partieRejoint->setUser($user);
        $partieRejoint->setPartie($partie);
        $partieRejoint->setRole("joueur");

        $em->persist($partieRejoint);
        $em->flush();

        $this->addFlash('success', 'Création réussie');

        return $this->redirectToRoute('app_partie');
    }


    #[Route('/partie/quitter/{id}', name: 'partieRejoint_quitter', methods: ['DELETE'])]
    public function quitter(PartieRejoint $partieRejoint, EntityManagerInterface $em)
    {
        $em->remove($partieRejoint);
        $em->flush();
        $this->addFlash('success', 'Vous avez quitté le groupe');

        return $this->redirectToRoute('app_partie');
    }
}
