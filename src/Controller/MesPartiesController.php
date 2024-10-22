<?php

namespace App\Controller;

use App\Entity\Choix;
use App\Entity\Partie;
use App\Entity\PartieRejoint;
use App\Entity\Restriction;
use App\Entity\TirageResultat;
use App\Entity\User;
use App\Form\PartieType;
use App\Form\RestrictionType;
use App\Form\SouhaitType;
use App\Repository\ChoixRepository;
use App\Repository\PartieRejointRepository;
use App\Repository\PartieRepository;
use App\Repository\RestrictionRepository;
use App\Repository\TirageResultatRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class MesPartiesController extends AbstractController
{

    #[Route('/mes-parties', name: 'app_mes_parties')]
    #[IsGranted('ROLE_USER')]
    public function index(PartieRejointRepository $partieRejointRepository, PartieRepository $partieRepository): Response
    {
        $userId = $this->getUser()->getId();
        $partiesRejoints = $partieRejointRepository->findByUserId($userId);
        $parties = $partieRepository->findAll();


        return $this->render('mes_parties/index.html.twig', [
            'controller_name' => 'MesPartiesController',
            'partiesRejoints' => $partiesRejoints,
            'parties' => $parties
        ]);
    }

    #[Route('/mes-parties/quitter/{id}', name: 'mesPartiesRejoints_quitter', methods: ['DELETE'])]
    public function quitter(PartieRejoint $partieRejoint, EntityManagerInterface $em)
    {
        $em->remove($partieRejoint);
        $em->flush();
        $this->addFlash('success', 'Vous avez quitté le groupe');
        return $this->redirectToRoute('app_mes_parties');
    }


    #[Route('/mes-parties/view/{id}', name: 'mes_parties_view')]
    public function view(RestrictionRepository $restrictionRepository, ChoixRepository $choixRepository, PartieRejointRepository $partieRejointRepository, PartieRepository $partieRepo, $id, UserRepository $userRepository, TirageResultatRepository $tirageResultatRepository): Response
    {
        $user = $this->getUser();
        $partie = $partieRepo->find($id);
        $users = $userRepository->findAll();
        $choix = $choixRepository->findBy(['partie' => $partie]);
        $choixFinal = "";
        $tirageResultatRepo = $tirageResultatRepository->findBy(['partie' => $partie]);
        $tirages = $tirageResultatRepository->findAll();
        $restrictions = $restrictionRepository->findAll();


        if ($choix) {
            $choixFinal = $choixRepository->findOneBy(['joueur' => $user, 'partie' => $partie])->getPersonneChoisie()->getUsername();
        }
        $roleUser = $partieRejointRepository->findOneBy([
            'partie' => $partie,
            'user' => $user
        ]);


        $partiesRejoints = $partieRejointRepository->findBy(['partie' => $partie]);

        return $this->render('mes_parties/view.html.twig', [
            'partie' => $partie,
            'partiesRejoints' => $partiesRejoints,
            'users' => $users,
            'tirageResultats' => $tirageResultatRepo,
            'tirages' => $tirages,
            'util' => $roleUser,
            'choix' => $choixFinal,
            'restrictions' => $restrictions
        ]);
    }


    #[Route('/mes-parties/{id}/edit', name: 'mes_parties_edit')]
    public function edit(Partie $partie, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $originalPassword = $partie->getPassword();
        $user = $this->getUser();

        $form = $this->createForm(PartieType::class, $partie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('no_password')->getData()) {
                $partie->setPassword(null);
            } else {
                if (empty($partie->getPassword())) {
                    $partie->setPassword($originalPassword);
                } else {
                    $plainPassword = $partie->getPassword();
                    $hashedPassword = $hasher->hashPassword($user, $plainPassword);
                    $partie->setPassword($hashedPassword);
                }
            }
            $em->flush();
            $this->addFlash('success', 'Modification Réussie');
            return $this->redirectToRoute('mes_parties_view', ['id' => $partie->getId()]);
        }
        return $this->render('partie/edit.html.twig', [
            'partie' => $partie,
            'form' => $form
        ]);
    }

    #[Route('/mes-parties/{id}', name: 'mes_parties_delete', methods: ['DELETE'])]
    public function remove(Partie $partie, EntityManagerInterface $em)
    {
        $em->remove($partie);
        $em->flush();
        $this->addFlash('success', 'Suppression Réussie');
        return $this->redirectToRoute('app_partie');
    }

    #[Route('/mes-partie/{id}/restriction/{idRestriction}', name: 'partie_restriction_delete', methods: ['DELETE'])]
    public function delete(RestrictionRepository $restrictionRepository ,int $idRestriction, Partie $partie, EntityManagerInterface $em)
    {
        $restriction = $restrictionRepository->find($idRestriction);
        $em->remove($restriction);
        $em->flush();
        $this->addFlash('success', 'Restriction supprimée');

        return $this->redirectToRoute('mes_parties_view', ['id' => $partie->getId()]);
    }




    #[Route('/mes-partie/{id}/tirage', name: 'partie_tirage')]
    public function tirageAuSort(RestrictionRepository $restrictionRepository, PartieRepository $partieRepository, PartieRejointRepository $partieRejointRepository, EntityManagerInterface $em, $id, UserRepository $userRepository): Response
    {
        $partie = $partieRepository->find($id);
        $partiesRejoints = $partieRejointRepository->findByPartieId($partie->getId());
        $restrictions = $restrictionRepository->findByPartieId($id);

        // Récupération des participants
        $participants = [];
        foreach ($partiesRejoints as $partieRejoint) {
            $participants[] = $partieRejoint['userId'];
        }

        // Vérification du nombre de participants
        if (count($participants) < 2) {
            $this->addFlash('error', 'Le tirage au sort nécessite au moins 2 participants.');
            return $this->redirectToRoute('mes_parties_view', ['id' => $id]);
        }

        // Copie des participants pour les destinataires
        $destinataires = $participants;

        do {
            shuffle($destinataires);

            $tirageValide = true;

            foreach ($participants as $index => $participantId) {
                $participant = $userRepository->find($participantId);
                $destinataire = $userRepository->find($destinataires[$index]);

                if ($participantId === $destinataires[$index]) {
                    $tirageValide = false;
                    break; // Quitte la boucle si un utilisateur se tire lui-même
                }

                if ($restrictions) {
                    if (!$this->tirageValideRestriction($partie->getId(), $participant, $destinataire, $restrictionRepository)) {
                        $tirageValide = false;
                        break;
                    }
                }
            }
        } while (!$tirageValide);

        // Sauvegarde du tirage dans la base de données
        foreach ($participants as $index => $participantId) {
            $participant = $userRepository->find($participantId);
            $destinataire = $userRepository->find($destinataires[$index]);

            $tirageResultat = new TirageResultat();
            $tirageResultat->setJoueur($participant);
            $tirageResultat->setDestinataire($destinataire);
            $tirageResultat->setPartie($partie);

            $em->persist($tirageResultat);
        }

        $em->flush();

        $this->addFlash('success', 'Le tirage au sort a été effectué avec succès !');

        return $this->redirectToRoute('mes_parties_view', ['id' => $id]);
    }

    private function tirageValide(array $participants, array $destinataires): bool
    {
        foreach ($participants as $index => $participant) {
            if ($participant === $destinataires[$index]) {
                return false;
            }
        }
        return true;
    }

    private function tirageValideRestriction(int $idPartie, User $joueur, User $interdit, RestrictionRepository $restrictionRepository): bool
    {
        // Récupère une restriction spécifique pour ce joueur, interdit, et partie
        $restriction = $restrictionRepository->findRestriction($idPartie, $joueur->getId(), $interdit->getId());

        // Si une restriction existe, le tirage n'est pas valide
        return $restriction === null;
    }


    #[Route('/mes-parties/terminer/{id}', name: 'partie_terminee', methods: ['POST'])]
    public function finPartie(RestrictionRepository $restrictionRepository, ChoixRepository $choixRepository, int $id, TirageResultatRepository $tirageResultatRepository, EntityManagerInterface $em, PartieRepository $partieRepository): Response
    {
        $partie = $partieRepository->find($id);
        if (!$partie) {
            $this->addFlash('error', 'Partie non trouvée.');
            return $this->redirectToRoute('app_mes_parties');
        }

        $tirages = $tirageResultatRepository->findBy(['partie' => $partie]);
        $choix = $choixRepository->findBy(['partie' => $partie]);
        $restrictions = $restrictionRepository->findBy(['partie' => $partie]);

        foreach ($tirages as $tirage) {
            $em->remove($tirage);
        }

        foreach ($choix as $ligne) {
            $em->remove($ligne);
        }

        // foreach ($restrictions as $ligne) {
        //     $em->remove($ligne);
        // }

        $em->flush();

        $this->addFlash('success', 'Partie terminée, tous les tirages ont été supprimés.');

        return $this->redirectToRoute('mes_parties_view', ['id' => $id]);
    }


    #[Route('/mes-parties/souhaits/{id}/{idUser}', name: 'souhaits_view')]
    public function voirSouhaits(PartieRejointRepository $partieRejointRepository, PartieRepository $partieRepo, $id, $idUser, UserRepository $userRepository, TirageResultatRepository $tirageResultatRepository): Response
    {
        $partie = $partieRepo->find($id);

        $userId = $this->getUser()->getId();
        $partiesRejoints = $partieRejointRepository->findByPartieId($partie->getId());
        foreach ($partiesRejoints as $partieRejoint) {
            if ($idUser == $partieRejoint['userId']) {
                $souhaits = $partieRejoint['souhaits'];
            }
        }
        $users = $userRepository->findAll();

        return $this->render('mes_parties/view.html.twig', [
            'partie' => $partie,
            'partiesRejoints' => $partiesRejoints,
            'users' => $users,
            'souhaits' => $souhaits
        ]);
    }

    #[Route('/mes-parties/{id}/addSouhait', name: 'mes_parties_addSouhait')]
    public function addSouhait(PartieRepository $partieRepo, PartieRejointRepository $partieRejointRepository, Request $request, EntityManagerInterface $em, $id): Response
    {
        $partie = $partieRepo->find($id);
        $user = $this->getUser();
        if (!$partie) {
            throw $this->createNotFoundException('La partie demandée n\'existe pas.');
        }

        $partieRejoint = $partieRejointRepository->findOneBy([
            'partie' => $partie,
            'user' => $user
        ]);
        if (!$partieRejoint) {
            throw $this->createNotFoundException('Le participant à la partie n\'existe pas.');
        }

        $form = $this->createForm(SouhaitType::class, $partieRejoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Modification Réussie');
            return $this->redirectToRoute('mes_parties_view', [
                'id' => $id,
            ]);
        }

        return $this->render('mes_parties/edit.html.twig', [
            'partieRejoint' => $partieRejoint,
            'form' => $form->createView()
        ]);
    }

    #[Route('/mes-parties/view/{id}/{idUser}', name: 'mes_parties_kick_user', methods: ['DELETE'])]
    public function kick(PartieRepository $partieRepo, RestrictionRepository $restrictionRepository, PartieRejointRepository $partieRejointRepository, int $idUser, int $id, EntityManagerInterface $em)
    {
        $partie = $partieRepo->find($id);
        $restriction = $restrictionRepository->findOneBy(['joueur' => $idUser, 'partie'=>$partie ]);
        if($restriction) {
            $em->remove($restriction);
        }
        $user = $partieRejointRepository->findOneBy(['user' => $idUser]);
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'Suppression Réussie');
        return $this->redirectToRoute('mes_parties_view', ['id' => $id]);
    }
}
