<?php

namespace App\Controller;

use App\Entity\Evenements;
use App\Entity\UserEvenement;
use App\Form\EvenementsType;
use App\Repository\EvenementsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evenements')]
final class EvenementsController extends AbstractController
{
    #[Route(name: 'app_evenements_index', methods: ['GET'])]
    public function index(EvenementsRepository $evenementsRepository): Response
    {
        // 🔒 Les utilisateurs non-admin/prof ne voient que les événements validés
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROF')) {
            $evenements = $evenementsRepository->findBy(['est_valide' => true]);
        } else {
            $evenements = $evenementsRepository->findAll();
        }

        return $this->render('evenements/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/new', name: 'app_evenements_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenements();

        // Création du formulaire avec l'option 'user_roles'
        $form = $this->createForm(EvenementsType::class, $evenement, [
            'user_roles' => $this->getUser() ? $this->getUser()->getRoles() : [],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Forcer est_valide à false si l'utilisateur n'est pas admin/prof
            if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROF')) {
                $evenement->setEstValide(false);
            }

            // Lier l'utilisateur courant comme responsable
            $userEvenement = new UserEvenement();
            $userEvenement->setRefUser($this->getUser());
            $userEvenement->setRefEvenement($evenement);
            $userEvenement->setIsResponsable(true);
            $evenement->addUserEvenement($userEvenement);

            $entityManager->persist($evenement);
            $entityManager->persist($userEvenement);
            $entityManager->flush();

            $this->addFlash('success', 'Événement créé avec succès.');

            return $this->redirectToRoute('app_evenements_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenements/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
            'title' => 'Créer un nouvel événement',
            'button_label' => 'Créer',
        ]);
    }

    #[Route('/{id}', name: 'app_evenements_show', methods: ['GET'])]
    public function show(Evenements $evenement): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROF') && !$evenement->isEstValide()) {
            throw $this->createAccessDeniedException('Cet événement n’est pas encore actif.');
        }

        return $this->render('evenements/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenements_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenements $evenement, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Vérifie si l'utilisateur est responsable via UserEvenement
        $isResponsable = $evenement->getUserEvenements()->exists(function ($key, $userEvenement) use ($user) {
            return $userEvenement->getRefUser() === $user && $userEvenement->isResponsable();
        });

        // Admins et profs peuvent tout modifier
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROF') && !$isResponsable) {
            throw $this->createAccessDeniedException('Vous n’avez pas la permission de modifier cet événement.');
        }

        $form = $this->createForm(EvenementsType::class, $evenement, [
            'user_roles' => $this->getUser() ? $this->getUser()->getRoles() : [],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Événement modifié avec succès.');

            return $this->redirectToRoute('app_evenements_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenements/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
            'title' => 'Modifier un événement',
            'button_label' => 'Enregistrer',
        ]);
    }

    #[Route('/{id}', name: 'app_evenements_delete', methods: ['POST'])]
    public function delete(Request $request, Evenements $evenement, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $isResponsable = $evenement->getUserEvenements()->exists(function ($key, $userEvenement) use ($user) {
            return $userEvenement->getRefUser() === $user && $userEvenement->isResponsable();
        });

        // Admins et profs peuvent tout supprimer
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROF') && !$isResponsable) {
            throw $this->createAccessDeniedException('Vous n’avez pas la permission de supprimer cet événement.');
        }

        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
            $this->addFlash('success', 'Événement supprimé avec succès.');
        }

        return $this->redirectToRoute('app_evenements_index', [], Response::HTTP_SEE_OTHER);
    }
}



