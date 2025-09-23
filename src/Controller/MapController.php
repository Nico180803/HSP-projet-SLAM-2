<?php

namespace App\Controller;

use App\Repository\EtablissementsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function index(EtablissementsRepository $repo): Response
    {
        // Récupère tous les établissements
        $etablissements = $repo->findAll();

        return $this->render('map/index.html.twig', [
            'etablissements' => $etablissements,
        ]);
    }
}

