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
        $etablissements = $repo->findAll();

        // Transformation en tableau
        $data = array_map(function($e) {
            return [
                'id' => $e->getId(),
                'mail' => $e->getMail(),
                'tel' => $e->getTel(),
                'nbRue' => $e->getNbRue(),
                'rue' => $e->getRue(),
                'ville' => $e->getVille(),
                'cp' => $e->getCp(),
                'latitude' => $e->getLatitude(),
                'longitude' => $e->getLongitude(),
            ];
        }, $etablissements);

        return $this->render('map/index.html.twig', [
            'etablissements' => $data,
        ]);
    }

}

