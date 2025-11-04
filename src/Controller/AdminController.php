<?php

namespace App\Controller;

use App\Entity\UserEvenement;
use App\Repository\EvenementsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EvenementsRepository $evenementsRepository, UserRepository $userRepository): Response
    {
        $upcommingEvent = $evenementsRepository->getNumberOfEvenements();
        $evenements = $evenementsRepository->getLastEvenements(5);
        $users = $userRepository->getLastUser(5,);

        if ($this->getUser()->getRole() != 'ROLE_ADMIN') {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'evenements' => $evenements,
            'users' => $users,
            'upcomingEvent' => $upcommingEvent,
        ]);
    }
}
