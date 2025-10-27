<?php

namespace App\Controller;


use App\Repository\EvenementsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EvenementsRepository $evenementsRepository): Response
    {
       $evenements = $evenementsRepository->getLastEvenements(3);

        return $this->render('home/index.html.twig',[
            'evenements' => $evenements]);
    }

    #[Route('/support', name: 'app_support')]
    public function support(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('sujet', TextType::class)
            ->add('demande', TextareaType::class)
            ->add('submit', SubmitType::class, ['label' => 'Envoyer'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $email = (new Email())
                    ->from('support.hsp@hoziodev.fr')
                    ->to($form->getData()['email'])
                    ->subject($form->getData()['sujet'])
                    ->text($form->getData()['demande']);
                $mailer->send($email);
                $this->addFlash('success', 'Reussi');
            }catch (\Exception $e){
                $this->addFlash('error', 'Erreur lors de l\'envoi du mail : ' . $e->getMessage());
            }
        }
        return $this->render('home/support.html.twig', [
            'demande' => $form->createView(),
        ]);
    }
}
