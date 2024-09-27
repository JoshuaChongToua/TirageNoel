<?php

namespace App\Controller;

use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RoleController extends AbstractController
{
    #[Route('/role', name: 'app_role')]
    public function index(RoleRepository $roleRepository): Response
    {
        $roles = $roleRepository->findAll();
        return $this->render('role/index.html.twig', [
            'controller_name' => 'RoleController',
            'roles' => $roles
        ]);
    }

    #[Route('/role/view/{id}', name: 'role_view')]
    public function view(RoleRepository $roleRepo, $id): Response
    {

        $role = $roleRepo->find($id);
        return $this->render('role/view.html.twig', [
            'role' => $role
        ]);
    }

    #[Route('/role/create', name: 'role_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($role);
            $em->flush();
            $this->addFlash('success', 'Creation Réussie');
            return $this->redirectToRoute('app_role');
        }
        return $this->render('role/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/role/{id}/edit', name: 'role_edit')]
    public function edit(Role $role, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Modification Réussie');
            return $this->redirectToRoute('app_role');
        }
        return $this->render('role/edit.html.twig', [
            'role' => $role,
            'form' => $form
        ]);
    }

    #[Route('/role/{id}', name: 'role_delete', methods: ['DELETE'])]
    public function remove(Role $role, EntityManagerInterface $em)
    {
        $em->remove($role);
        $em->flush();
        $this->addFlash('success', 'Suppression Réussie');
        return $this->redirectToRoute('app_role');
    }
}
