<?php

namespace App\Controller;

use App\Entity\Offres;
use App\Form\OffresType;
use App\Repository\OffresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/offres')]
final class OffresController extends AbstractController
{
    #[Route(name: 'app_offres_index', methods: ['GET'])]
    public function index(OffresRepository $offresRepository): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('offres/index.html.twig', [
            'offres' => $offresRepository->findAll(),
        ]);
    }
    #[Route('/view', name: 'app_offres_view', methods: ['GET'])]
    public function view(PaginatorInterface $paginator, Request $request, OffresRepository $offresRepository): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }

        $status = $request->query->get('status', 'tous');
        $search = trim((string) $request->query->get('search', ''));


        $qb = $offresRepository->createQueryBuilder('o');
        $qb->andWhere('o.date_fermeture > :today')
            ->setParameter('today', new \DateTime());


        if (in_array('ROLE_ENTREPRISE', $this->getUser()->getRoles(), true)) {
            $qb->andWhere('o.refCreateur = :user')
                ->setParameter('user', $this->getUser());
        }
        if ($status === "mine") {
            $qb->andWhere(':user MEMBER OF o.refUser')
                ->setParameter('user', $this->getUser());
        }

        if ($search !== '') {
            $qb->andWhere('o.titre LIKE :search OR o.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }


        $qb->orderBy('o.date_creation', 'DESC');

        // Pagination
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('offres/view.html.twig', [
            'pagination' => $pagination,
            'search'     => $search,
            'status'     => $status,
        ]);
    }


    #[Route('/new', name: 'app_offres_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            if (!in_array('ROLE_ENTREPRISE', $this->getUser()->getRoles())){
                return $this->redirectToRoute('app_home');
            }

        }
        $offre = new Offres();
        $form = $this->createForm(OffresType::class, $offre, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);
        $offre->setRefCreateur($this->getUser());
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offre);
            $entityManager->flush();

            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
                return $this->redirectToRoute('app_offres_index', [], Response::HTTP_SEE_OTHER);
            }else{
                return $this->redirectToRoute('app_offres_view', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('offres/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/offres/{id}/postuler', name: 'app_offres_postuler', methods: ['POST','GET'])]
    public function postuler(Offres $offre, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }


        $offre->addRefUser($user);

        $entityManagerInterface->persist($offre);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Votre candidature a été enregistrée.');

        return $this->redirectToRoute('app_offres_view');
    }
    #[Route('/offres/{id}/candidatures', name: 'app_offres_candidatures', methods: ['GET'])]
    public function candidatures(Offres $offre): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            if (!in_array('ROLE_ENTREPRISE', $this->getUser()->getRoles())){
                return $this->redirectToRoute('app_home');
            }

        }

        $candidats = $offre->getRefUser();

        return $this->render('offres/candidatures.html.twig', [
            'offre'     => $offre,
            'candidats' => $candidats,
        ]);
    }


    #[Route('/{id}', name: 'app_offres_show', methods: ['GET'])]
    public function show(Offres $offre): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
                return $this->redirectToRoute('app_home');
        }
        return $this->render('offres/show.html.twig', [
            'offre' => $offre,
        ]);
    }
    #[Route('/{id}/showOffre', name: 'app_offres_showOffre', methods: ['GET'])]
    public function showOffre(Offres $offre): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('offres/showOffre.html.twig', [
            'offre' => $offre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offres_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offres $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            if (!in_array('ROLE_ENTREPRISE', $this->getUser()->getRoles())){
                return $this->redirectToRoute('app_home');
            }

        }
        $form = $this->createForm(OffresType::class, $offre, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
                return $this->redirectToRoute('app_offres_index', [], Response::HTTP_SEE_OTHER);
            }else{
                return $this->redirectToRoute('app_offres_view', [], Response::HTTP_SEE_OTHER);
            }

        }

        return $this->render('offres/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/editOffre', name: 'app_offres_editOffre', methods: ['GET', 'POST'])]
    public function editOffre(Request $request, Offres $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(OffresType::class, $offre, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_offres_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offres/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offres_delete', methods: ['POST'])]
    public function delete(Request $request, Offres $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            if (!in_array('ROLE_ENTREPRISE', $this->getUser()->getRoles())){
                return $this->redirectToRoute('app_home');
            }

        }
        if ($this->isCsrfTokenValid('delete'.$offre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_offres_index', [], Response::HTTP_SEE_OTHER);
        }else{
            return $this->redirectToRoute('app_offres_view', [], Response::HTTP_SEE_OTHER);
        }
    }
    #[Route('/offres/{id}/annuler', name: 'app_offres_annuler', methods: ['GET', 'POST'])]
    public function annuler(Offres $offre, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }


        if ($offre->getRefUser()->contains($user)) {
            $offre->removeRefUser($user);
            $entityManagerInterface->persist($offre);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Votre candidature a été annulée.');
        }

        return $this->redirectToRoute('app_offres_view');
    }

}
