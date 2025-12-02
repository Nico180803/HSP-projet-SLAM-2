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
    #[Route('/', name: 'app_evenements_index', methods: ['GET'])]
    public function index(EvenementsRepository $evenementsRepository): Response
    {
        $user = $this->getUser();

        // Les non-admins ne voient que les événements validés ou dont ils sont responsables
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_MEDECIN')) {
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

    #[Route('/evenements/non-actif', name: 'app_evenements_non_actif', methods: ['GET'])]
    public function evenementAValider(EvenementsRepository $evenementsRepository): Response
    {
        $evenements = $evenementsRepository->createQueryBuilder('e')
            ->andWhere('e.est_valide = false')
            ->getQuery()
            ->getResult();

        return $this->render('evenements/non_actif.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/evenements/mes-evenements', name: 'app_evenements_mes_evenements', methods: ['GET'])]
    public function mesEvenements(EvenementsRepository $evenementsRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupère les évènements dont l'utilisateur est responsable
        $qb = $evenementsRepository->createQueryBuilder('e')
            ->leftJoin('e.userEvenements', 'ue')
            ->andWhere('ue.refUser = :user')
            ->andWhere('ue.isResponsable = true')
            ->setParameter('user', $user);

        $evenements = $qb->getQuery()->getResult();

        return $this->render('evenements/mes_evenements.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/evenements/mes-inscriptions', name: 'app_evenements_mes_inscriptions', methods: ['GET'])]
    public function mesInscriptions(EvenementsRepository $evenementsRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupère les évènements aux quels l'utilisateur s'est inscrit
        $qb = $evenementsRepository->createQueryBuilder('e')
            ->leftJoin('e.userEvenements', 'ue')
            ->andWhere('ue.refUser = :user')
            ->andWhere('ue.isResponsable = false')
            ->setParameter('user', $user);

        $evenements = $qb->getQuery()->getResult();

        return $this->render('evenements/mes_inscriptions.html.twig', [
            'evenements' => $evenements,
        ]);
    }


    #[Route('/new', name: 'app_evenements_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $evenement = new Evenements();
        $user = $this->getUser();

        $form = $this->createForm(EvenementsType::class, $evenement, [
            'user_roles' => $user ? $user->getRoles() : [],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie la cohérence des dates
            if ($evenement->getDateFin() < $evenement->getDateDebut()) {
                $this->addFlash('error', 'La date de fin ne peut pas être avant la date de début.');
                return $this->render('evenements/new.html.twig', [
                    'evenement' => $evenement,
                    'form' => $form->createView(),
                    'title' => 'Créer un nouvel événement',
                    'button_label' => 'Créer',
                ]);
            }

            // Si l’utilisateur n’est pas admin/prof, l’événement doit être validé plus tard
            if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_MEDECIN')) {
                $evenement->setEstValide(false);
            }

            $em->persist($evenement);

            // Créateur = responsable
            $creator = new UserEvenement();
            $creator->setRefUser($user);
            $creator->setRefEvenement($evenement);
            $creator->setIsResponsable(true);
            $em->persist($creator);

            // Responsables supplémentaires
            $responsables = $form->get('responsables')->getData();
            foreach ($responsables as $responsable) {
                if ($responsable !== $user) {
                    $ue = new UserEvenement();
                    $ue->setRefUser($responsable);
                    $ue->setRefEvenement($evenement);
                    $ue->setIsResponsable(true);
                    $em->persist($ue);
                }
            }

            $em->flush();
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

        // Restrictions d’accès
        if (
            !$this->isGranted('ROLE_ADMIN') &&
            !$this->isGranted('ROLE_MEDECIN') &&
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
    public function edit(Request $request, Evenements $evenement, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $isResponsable = $evenement->getUserEvenements()->exists(function ($key, $ue) use ($user) {
            return $ue->getRefUser() === $user && $ue->isResponsable();
        });

        // Vérifie les droits
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_MEDECIN') && !$isResponsable) {
            throw $this->createAccessDeniedException('Vous n’avez pas la permission de modifier cet événement.');
        }

        $form = $this->createForm(EvenementsType::class, $evenement, [
            'user_roles' => $user ? $user->getRoles() : [],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie les dates
            if ($evenement->getDateFin() < $evenement->getDateDebut()) {
                $this->addFlash('error', 'La date de fin ne peut pas être avant la date de début.');
                return $this->render('evenements/edit.html.twig', [
                    'evenement' => $evenement,
                    'form' => $form->createView(),
                    'title' => 'Modifier un événement',
                    'button_label' => 'Enregistrer',
                ]);
            }

            // Supprime les anciens responsables (sauf celui en cours)
            foreach ($evenement->getUserEvenements() as $userEvenement) {
                if ($userEvenement->isResponsable() && $userEvenement->getRefUser() !== $user) {
                    $em->remove($userEvenement);
                }
            }

            // Ajoute les nouveaux responsables
            $responsables = $form->get('responsables')->getData();
            foreach ($responsables as $responsable) {
                if ($responsable !== $user) {
                    $ue = new UserEvenement();
                    $ue->setRefUser($responsable);
                    $ue->setRefEvenement($evenement);
                    $ue->setIsResponsable(true);
                    $em->persist($ue);
                }
            }

            $em->flush();
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

    #[Route('/{id}/delete', name: 'app_evenements_delete', methods: ['POST'])]
    public function delete(Request $request, Evenements $evenement, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $isResponsable = $evenement->getUserEvenements()->exists(function ($key, $ue) use ($user) {
            return $ue->getRefUser() === $user && $ue->isResponsable();
        });

        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_MEDECIN') && !$isResponsable) {
            throw $this->createAccessDeniedException('Vous n’avez pas la permission de supprimer cet événement.');
        }

        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->request->get('_token'))) {
            $em->remove($evenement);
            $em->flush();
            $this->addFlash('success', 'Événement supprimé avec succès.');
        }

        return $this->redirectToRoute('app_evenements_index');
    }

    // Pour s'inscrire
    #[Route('/{id}/join', name: 'app_evenement_join')]
    public function join(Evenements $evenement, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        // Déjà inscrit ?
        $alreadyRegistered = $evenement->getUserEvenements()->exists(function($key, $ue) use ($user) {
            return $ue->getRefUser() === $user;
        });

        if ($alreadyRegistered) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit.');
            return $this->redirectToRoute('app_evenements_show', ['id' => $evenement->getId()]);
        }

        $userEvenement = new UserEvenement();
        $userEvenement->setRefUser($user);
        $userEvenement->setRefEvenement($evenement);
        $userEvenement->setIsResponsable(false);
        $em->persist($userEvenement);

        $evenement->setNbPlacesDispo($evenement->getNbPlacesDispo() - 1);

        $em->flush();
        $this->addFlash('success', 'Vous êtes inscrit !');

        return $this->redirectToRoute('app_evenements_show', ['id' => $evenement->getId()]);
    }

// Pour se désinscrire
    #[Route('/{id}/leave', name: 'app_evenement_leave')]
    public function leave(Evenements $evenement, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        $userEvenement = null;
        foreach ($evenement->getUserEvenements() as $ue) {
            if ($ue->getRefUser()->getId() === $user->getId() && !$ue->isResponsable()) {
                $userEvenement = $ue;
                break;
            }
        }

        if (!$userEvenement) {
            $this->addFlash('warning', 'Vous n’êtes pas inscrit ou êtes responsable.');
            return $this->redirectToRoute('app_evenements_show', ['id' => $evenement->getId()]);
        }

        $em->remove($userEvenement);
        $evenement->setNbPlacesDispo($evenement->getNbPlacesDispo() + 1);
        $em->flush();

        $this->addFlash('success', 'Vous vous êtes désinscrit.');
        return $this->redirectToRoute('app_evenements_show', ['id' => $evenement->getId()]);
    }
}
