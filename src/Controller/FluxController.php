<?php

namespace App\Controller;
use App\Entity\Commentaires;
use App\Entity\Flux;
use App\Entity\Sujets;
use App\Entity\User;
use App\Form\FluxType;
use App\Repository\FluxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/flux')]
final class FluxController extends AbstractController
{
    #[Route(name: 'app_flux_index', methods: ['GET'])]
    public function index(FluxRepository $fluxRepository): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('flux/index.html.twig', [
            'fluxes' => $fluxRepository->findAll(),
        ]);
    }

    #[Route('/newFlux', name: 'app_flux_newFlux', methods: ['GET', 'POST'])]
    public function newFlux(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        $flux = new Flux();
        $form = $this->createForm(FluxType::class, $flux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($flux);
            $entityManager->flush();

            return $this->redirectToRoute('app_flux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('flux/new.html.twig', [
            'flux' => $flux,
            'form' => $form,
        ]);
    }
    #[Route('/new', name: 'app_flux_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if ($this->getUser()->getRoles()) {
            return $this->redirectToRoute('app_home');
        }
        $titre = $request->request->get('titre');
        $message = $request->request->get('message');
        $refFluxId = $request->request->get('refFluxId');
        $reponse = $request->request->get('reponse');
        $refSujetId = $request->request->get('refSujetId');

        //valeur test car pas user pour le moment STP NICOLASSSSS
        $user = $this->getUser() ?? $entityManager->getRepository(User::class)->find(1);
        //reponse
        if ($reponse && $refSujetId) {
            $sujet = $entityManager->getRepository(Sujets::class)->find($refSujetId);

            $commentaire = new Commentaires();
            $commentaire->setReponse($reponse);
            $commentaire->setRefSujet($sujet);
            $commentaire->setRefUser($user);
            $commentaire->setDateCreation(new \DateTime());

            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->json(['message' => 'Commentaire ajouté avec succès !'], 201);
        }

        //sujet
        if ($titre && $message && $refFluxId) {
            $flux = $entityManager->getRepository(Flux::class)->find($refFluxId);


            $sujet = new Sujets();
            $sujet->setTitre($titre);
            $sujet->setMessage($message);
            $sujet->setRefUser($user);
            $sujet->setDateCreation(new \DateTime());
            $sujet->setRefFlux($flux);

            $entityManager->persist($sujet);
            $entityManager->flush();

            return $this->json(['message' => 'Sujet ajouté avec succès !'], 201);
        }


        return $this->json(['error' => 'Requête invalide. Données manquantes.'], 400);
    }



    #[Route('/{id}', name: 'app_flux_show', methods: ['GET'])]
    public function show(Flux $flux,FluxRepository $fluxRepository,PaginatorInterface $paginator,Request $request,EntityManagerInterface $entityManager): Response
    {

        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (count(array_intersect($flux->getRole(), $this->getUser()->getRoles())) == 0) {
            return $this->redirectToRoute('app_home');
        }
        $search = $request->query->get('search', null);
        $sujetRepository = $entityManager->getRepository(Sujets::class);
        $queryBuilder = $sujetRepository->createQueryBuilder('s')
            ->where('s.refFlux = :flux')
            ->setParameter('flux', $flux);


        if ($search) {
            $queryBuilder
                ->andWhere('LOWER(s.titre) LIKE :search OR LOWER(s.message) LIKE :search')
                ->setParameter('search', '%' . strtolower($search) . '%');
        }

        $query = $queryBuilder->getQuery();
        $query = array_reverse($query->getResult());
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('flux/show.html.twig', [
            'flux' => $flux,
            'sujets' =>array_reverse($flux->getSujets()->toArray()),
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_flux_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Flux $flux, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(FluxType::class, $flux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_flux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('flux/edit.html.twig', [
            'flux' => $flux,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_flux_delete', methods: ['POST'])]
    public function delete(Request $request, Flux $flux, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        if ($this->isCsrfTokenValid('delete'.$flux->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($flux);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_flux_index', [], Response::HTTP_SEE_OTHER);
    }

}
