<?php

namespace App\Controller;

use App\Entity\Flux;
use App\Entity\Sujets;
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



    #[Route('/new', name: 'app_flux_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $flux = new Flux();
        $sujet = new Sujets();




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
