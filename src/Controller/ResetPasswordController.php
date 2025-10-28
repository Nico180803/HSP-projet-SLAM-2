<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class ResetPasswordController extends AbstractController
{
    #[Route('/MotdePasseOublie', name: 'app_demande_reset_password')]
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
                        ->subject($form->getData()['Réinitialisation de votre mot de passe'])
                        ->text('http://127.0.0.1:8000/reset_password/?token=' . $user->getResetToken());
                    $mailer->send($email);
                    $this->addFlash('success', 'Reussi');
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
    public function resetPassword(Request $request): Response
    {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);


        return $this->render('reset_password/reset_password.html.twig', [
            'controller_name' => 'ResetPasswordController',

        ]);
    }
}
