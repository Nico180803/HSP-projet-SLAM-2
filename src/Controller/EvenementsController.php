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
        $user = $this->getUser();

        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROF')) {
            // L'utilisateur voit les événements validés ou ceux dont il est responsable
            $evenements = $evenementsRepository->createQueryBuilder('e')
                ->leftJoin('e.userEvenements', 'ue')
                ->andWhere('e.est_valide = true OR (ue.refUser = :user AND ue.isResponsable = true)')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();
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
        $user = $this->getUser();

        $form = $this->createForm(EvenementsType::class, $evenement, [
            'user_roles' => $user ? $user->getRoles() : [],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si l'utilisateur n'est pas admin/prof, l'événement reste non validé
            if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROF')) {
                $evenement->setEstValide(false);
            }

            // Le créateur est toujours responsable
            $creator = new UserEvenement();
            $creator->setRefUser($user);
            $creator->setRefEvenement($evenement);
            $creator->setIsResponsable(true);
            $entityManager->persist($creator);

            // Autres responsables choisis
            $responsables = $form->get('responsables')->getData();
            foreach ($responsables as $responsable) {
                if ($responsable !== $user) {
                    $userEvenement = new UserEvenement();
                    $userEvenement->setRefUser($responsable);
                    $userEvenement->setRefEvenement($evenement);
                    $userEvenement->setIsResponsable(true);
                    $entityManager->persist($userEvenement);
                }
            }

            $entityManager->persist($evenement);
            $entityManager->flush();

            $this->addFlash('success', 'Événement créé avec succès.');

            return $this->redirectToRoute('app_evenements_index');
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
        $user = $this->getUser();
        $isResponsable = $evenement->getUserEvenements()->exists(function($key, $ue) use ($user) {
            return $ue->getRefUser() === $user && $ue->isResponsable();
        });

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            !$this->isGranted('ROLE_PROF') &&
            !$evenement->isEstValide() &&
            !$isResponsable
        ) {
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

        $isResponsable = $evenement->getUserEvenements()->exists(function ($key, $ue) use ($user) {
            return $ue->getRefUser() === $user && $ue->isResponsable();
        });

        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROF') && !$isResponsable) {
            throw $this->createAccessDeniedException('Vous n’avez pas la permission de modifier cet événement.');
        }

        $form = $this->createForm(EvenementsType::class, $evenement, [
            'user_roles' => $user ? $user->getRoles() : [],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($evenement->getUserEvenements() as $userEvenement) {
                if ($userEvenement->isResponsable() && $userEvenement->getRefUser() !== $user) {
                    $entityManager->remove($userEvenement);
                }
            }

            $responsables = $form->get('responsables')->getData();
            foreach ($responsables as $responsable) {
                if ($responsable !== $user) {
                    $userEvenement = new UserEvenement();
                    $userEvenement->setRefUser($responsable);
                    $userEvenement->setRefEvenement($evenement);
                    $userEvenement->setIsResponsable(true);
                    $entityManager->persist($userEvenement);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Événement modifié avec succès.');

            return $this->redirectToRoute('app_evenements_index');
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
        $isResponsable = $evenement->getUserEvenements()->exists(function ($key, $ue) use ($user) {
            return $ue->getRefUser() === $user && $ue->isResponsable();
        });

        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROF') && !$isResponsable) {
            throw $this->createAccessDeniedException('Vous n’avez pas la permission de supprimer cet événement.');
        }

        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
            $this->addFlash('success', 'Événement supprimé avec succès.');
        }

        return $this->redirectToRoute('app_evenements_index');
    }
}





