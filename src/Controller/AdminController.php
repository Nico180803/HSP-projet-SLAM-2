<?php

namespace App\Controller;

use App\Repository\EvenementsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EvenementsRepository $evenementsRepository): Response
    {
        $upcommingEvent = $evenementsRepository->getNumberOfEvenements();
        $evenements = $evenementsRepository->getLastEvenements(5);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'evenements' => $evenements,
            'upcomingEvent' => $upcommingEvent,
        ]);
    }
}
