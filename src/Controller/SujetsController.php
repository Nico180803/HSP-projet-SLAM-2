<?php

namespace App\Controller;

use App\Entity\Sujets;
use App\Form\SujetsType;
use App\Repository\SujetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;

#[Route('/sujets')]
final class SujetsController extends AbstractController
{

    #[Route('/{id}', name: 'app_sujets_delete', methods: ['POST'])]
    public function delete(Request $request, Sujets $sujet, EntityManagerInterface $entityManager): Response
    {
        $referer = $request->headers->get('referer');
        if ($this->isCsrfTokenValid('delete'.$sujet->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sujet);
            $entityManager->flush();
        }


        return $this->redirect($referer);
    }
}
