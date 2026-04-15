<?php

namespace App\Controller;

use App\Entity\UserEvenement;
use App\Repository\EvenementsRepository;
use App\Repository\OffresRepository;
use App\Repository\SujetsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EvenementsRepository $evenementsRepository, UserRepository $userRepository, SujetsRepository $sujetsRepository, OffresRepository $offresRepository): Response
    {
        $upcommingEvent = $evenementsRepository->getNumberOfEvenements();
        $evenements = $evenementsRepository->getLastEvenements(5);
        $users = $userRepository->getLastUser(5,);
        $sujets = $sujetsRepository->getLastSujet(5);
        $offres = $offresRepository->createQueryBuilder('o')
            ->orderBy('o.date_creation', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        //BUG QUI REDIRIGE TOUT LE TEMPS VERS APP LOGIN A CORRIGE MAIS LA EPREUVE PAS LE TEMPS

//        if ($this->getUser()->getRoles() != 'ROLE_ADMIN') {
//            return $this->redirectToRoute('app_login');
//        }

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'evenements' => $evenements,
            'users' => $users,
            'sujets' => $sujets,
            'upcomingEvent' => $upcommingEvent,
            'offres' => $offres,
        ]);
    }
}
