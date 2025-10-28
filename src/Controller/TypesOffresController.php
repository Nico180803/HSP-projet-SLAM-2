<?php

namespace App\Controller;

use App\Entity\TypesOffres;
use App\Form\TypesOffresType;
use App\Repository\TypesOffresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/types/offres')]
final class TypesOffresController extends AbstractController
{
    #[Route(name: 'app_types_offres_index', methods: ['GET'])]
    public function index(TypesOffresRepository $typesOffresRepository): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('types_offres/index.html.twig', [
            'types_offres' => $typesOffresRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_types_offres_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        $typesOffre = new TypesOffres();
        $form = $this->createForm(TypesOffresType::class, $typesOffre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typesOffre);
            $entityManager->flush();

            return $this->redirectToRoute('app_types_offres_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('types_offres/new.html.twig', [
            'types_offre' => $typesOffre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_types_offres_show', methods: ['GET'])]
    public function show(TypesOffres $typesOffre): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('types_offres/show.html.twig', [
            'types_offre' => $typesOffre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_types_offres_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypesOffres $typesOffre, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        $form = $this->createForm(TypesOffresType::class, $typesOffre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_types_offres_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('types_offres/edit.html.twig', [
            'types_offre' => $typesOffre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_types_offres_delete', methods: ['POST'])]
    public function delete(Request $request, TypesOffres $typesOffre, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        if ($this->isCsrfTokenValid('delete'.$typesOffre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($typesOffre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_types_offres_index', [], Response::HTTP_SEE_OTHER);
    }
}
