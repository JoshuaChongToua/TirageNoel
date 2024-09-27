<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\VoitureRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    #[Route('/categories', name: 'app_categorie')]
    #[IsGranted('ROLE_USER')]

    public function index(CategorieRepository $categorieRepository): Response
    {
        $listeCategories = $categorieRepository->findAll();
        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
            'categories' => $listeCategories
        ]);
    }
    
    #[Route('/categories/{id}', name: 'categorie_show')]
    public function show(CategorieRepository $categorieRepository, $id, VoitureRepository $voitureRepository): Response
    {
        $categorie = $categorieRepository->find($id);
        $voitures = $voitureRepository->findByCat($id);
        return $this->render('categorie/view.html.twig', [
            'voitures' => $voitures,
            'categorie'=>$categorie
        ]);
    }

    #[Route('/categorie/create', name: 'categorie_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();
            $this->addFlash('success', 'Creation Réussie');
            return $this->redirectToRoute('app_categorie');
        }
        return $this->render('categorie/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/categorie/{id}', name: 'categorie_delete', methods: ['DELETE'])]
    public function remove(Categorie $categorie, EntityManagerInterface $em)
    {
        $em->remove($categorie);
        $em->flush();
        $this->addFlash('success', 'Suppression Réussie');
        return $this->redirectToRoute('app_categorie');
    }

    #[Route('/categorie/{id}/edit', name: 'categorie_edit')]
    public function edit(Categorie $categorie, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Modification Réussie');
            return $this->redirectToRoute('app_categorie');
        }
        return $this->render('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form
        ]);
    }
}
