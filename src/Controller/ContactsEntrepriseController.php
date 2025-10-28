<?php

namespace App\Controller;

use App\Entity\ContactsEntreprise;
use App\Form\ContactsEntrepriseType;
use App\Repository\ContactsEntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contacts/entreprise')]
final class ContactsEntrepriseController extends AbstractController
{
    #[Route(name: 'app_contacts_entreprise_index', methods: ['GET'])]
    public function index(ContactsEntrepriseRepository $contactsEntrepriseRepository): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ENTREPRISE', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        $user = $this->getUser()->getId();

        $contactsEntreprises = $contactsEntrepriseRepository->findBy([
            'refEntreprise' => $user,
        ]);
        return $this->render('contacts_entreprise/index.html.twig', [
            'contacts_entreprises' => $contactsEntreprises,
        ]);
    }

    #[Route('/new', name: 'app_contacts_entreprise_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ENTREPRISE', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        $contactsEntreprise = new ContactsEntreprise();
        $form = $this->createForm(ContactsEntrepriseType::class, $contactsEntreprise);
        $form->handleRequest($request);
        $contactsEntreprise->setRefEntreprise($this->getUser()->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contactsEntreprise);
            $entityManager->flush();

            return $this->redirectToRoute('app_contacts_entreprise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contacts_entreprise/new.html.twig', [
            'contacts_entreprise' => $contactsEntreprise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contacts_entreprise_show', methods: ['GET'])]
    public function show(ContactsEntreprise $contactsEntreprise): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ENTREPRISE', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('contacts_entreprise/show.html.twig', [
            'contacts_entreprise' => $contactsEntreprise,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contacts_entreprise_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ContactsEntreprise $contactsEntreprise, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ENTREPRISE', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        $form = $this->createForm(ContactsEntrepriseType::class, $contactsEntreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contacts_entreprise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contacts_entreprise/edit.html.twig', [
            'contacts_entreprise' => $contactsEntreprise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contacts_entreprise_delete', methods: ['POST'])]
    public function delete(Request $request, ContactsEntreprise $contactsEntreprise, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ENTREPRISE', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
        if ($this->isCsrfTokenValid('delete'.$contactsEntreprise->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contactsEntreprise);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contacts_entreprise_index', [], Response::HTTP_SEE_OTHER);
    }
}
