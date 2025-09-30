<?php

namespace App\Controller;
use App\Entity\Commentaires;
use App\Entity\Flux;
use App\Entity\Sujets;
use App\Entity\User;
use App\Form\FluxType;
use App\Repository\FluxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/flux')]
final class FluxController extends AbstractController
{


    #[Route('/new', name: 'app_flux_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $titre = $request->request->get('titre');
        $message = $request->request->get('message');
        $refFluxId = $request->request->get('refFluxId');
        $reponse = $request->request->get('reponse');
        $refSujetId = $request->request->get('refSujetId');

        //valeur test car pas user pour le moment STP NICOLASSSSS
        $user = $this->getUser() ?? $entityManager->getRepository(User::class)->find(1);

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

        // 🟦 2. Création d'un sujet
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
    public function show(Flux $flux): Response
    {
        return $this->render('flux/show.html.twig', [
            'flux' => $flux,
            'sujets' => $flux->getSujets(),

        ]);
    }


}
