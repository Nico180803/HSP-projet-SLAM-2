<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $plainPassword)
            );


            $user->setDateCreation(new \DateTime());

            // Handle file upload (CV)
            /** @var UploadedFile|null $cvFile */
            $cvFile = $form->get('cv')->getData();

            if ($cvFile) {
                $newFilename = uniqid().'.'.$cvFile->guessExtension();


                    $cvFile->move(
                        $this->getParameter('cv_directory'), // Destination defined in services.yaml
                        $newFilename
                    );


                // Store file name in DB
                $user->setCv($newFilename);
            }


            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

}
