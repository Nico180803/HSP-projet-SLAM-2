<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ResetPasswordController extends AbstractController
{
    #[Route('/motDePasseOublie', name: 'app_demande_reset_password')]
    public function index(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('submit', SubmitType::class, ['label' => 'Envoyer le lien de réinitialisation'])
            ->getForm();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $form->getData()['email']]);
                $user->setResetToken(uniqid());
                $entityManager->flush();
                try {
                    $email = (new Email())
                        ->from('support.hsp@hoziodev.fr')
                        ->to($form->getData()['email'])
                        ->subject('Réinitialisation de votre mot de passe')
                        ->text('http://127.0.0.1:8000/reset_password/?token=' . $user->getResetToken());
                    $mailer->send($email);
                    $this->addFlash('success', 'Un mail vous a été envoyé');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'envoi du mail : ' . $e->getMessage());
                }
            }catch (\Exception $e){
                $this->addFlash('error', 'le compte n\'existe pas : ' . $e->getMessage());
            }

        }

        return $this->render('reset_password/index.html.twig', [
            'controller_name' => 'ResetPasswordController',
            'demande'  => $form->createView(),
        ]);
    }

    #[Route('/reset_password', name: 'app_reset_password')]
    public function resetPassword(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $resetToken = $request->get('token');
        $user = $entityManager->getRepository(User::class)->findOneBy(['reset_token' => $resetToken]);
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createFormBuilder()
            ->add('mdp', PasswordType::class)
            ->add('mdpConfirm', PasswordType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('mdpConfirm')->getData() == $form->get('mdp')->getData()) {
            try {
                $plainPassword = $form->get('mdp')->getData();
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $plainPassword)
                );
                $user->setResetToken(null);
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_login');
            }catch (\Exception $e){
                $this->addFlash('error', 'Erreur lors du changement de mot de passe : ' . $e->getMessage());
            }
        }


        return $this->render('reset_password/reset_password.html.twig', [
            'controller_name' => 'ResetPasswordController',
            'form' => $form->createView(),

        ]);
    }
}
