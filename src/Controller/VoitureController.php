<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\EntityManager;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class VoitureController extends AbstractController
{
    #[Route('/voitures/', name: 'app_voiture')]
    #[IsGranted('ROLE_USER')]
    public function index(VoitureRepository $voitureRepo): Response
    {
        $listeVoiture = $voitureRepo->findAll();
        $total = $voitureRepo->findHowMany();
        return $this->render('voiture/index.html.twig', [
            'controller_name' => 'VoitureController',
            'voitures' => $listeVoiture,
            'total' => $total
        ]);
    }

    #[Route('/voiture/view/{id}', name: 'voiture_view')]
    public function view(VoitureRepository $voitureRepo, $id): Response
    {

        $voiture = $voitureRepo->find($id);
        return $this->render('voiture/view.html.twig', [
            'voiture' => $voiture
        ]);
    }

    #[Route('/voiture/create', name: 'voiture_create')]
    public function create( Request $request, EntityManagerInterface $em): Response
    {
        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('image')->getData();
            $fileName = $voiture->getId() . '.' . $file->getClientOriginalExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/public/images/voitures', $fileName);
            $voiture->setImage($fileName);
            $em->persist($voiture);
            $em->flush();
            $this->addFlash('success', 'Creation Réussie');
            return $this->redirectToRoute('app_voiture');
        }
        return $this->render('voiture/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/voiture/{id}/edit', name: 'voiture_edit')]
    public function edit(Voiture $voiture, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('image')->getData();
            $fileName = $voiture->getId() . '.' . $file->getClientOriginalExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/public/images/voitures', $fileName);
            $voiture->setImage($fileName);
            $em->flush();
            $this->addFlash('success', 'Modification Réussie');
            return $this->redirectToRoute('app_voiture');
        }
        return $this->render('voiture/edit.html.twig', [
            'voiture' => $voiture,
            'form' => $form
        ]);
    }

    #[Route('/voiture/{id}', name: 'voiture_delete', methods:['DELETE'])]
    public function remove(Voiture $voiture, EntityManagerInterface $em)
    {
        $em->remove($voiture);
        $em->flush();
        $this->addFlash('success', 'Suppression Réussie');
        return $this->redirectToRoute('app_voiture');
    }
}
