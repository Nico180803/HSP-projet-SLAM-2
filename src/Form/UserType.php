<?php

namespace App\Form;

use App\Entity\Etablissements;
use App\Entity\Evenements;
use App\Entity\Offres;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Etudiant' => 'ROLE_USER',
                    'Professeur' => 'ROLE_PROF',
                    'Entreprise' => 'ROLE_ENTREPRISE',
                ],
                'multiple' => true,
                'expanded' => true,

            ])
            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('cv')
            ->add('specialite')
            ->add('cpEntreprise')
            ->add('rueEntreprise')
            ->add('villeEntreprise')
            ->add('pays')
            ->add('formation')
            ->add('EstValide')
            ->add('date_creation')
            ->add('nb_rueEntreprise')
            ->add('refEtablissement', EntityType::class, [
                'class' => Etablissements::class,
                'choice_label' => 'id',
            ])
            ->add('candidatures', EntityType::class, [
                'class' => Offres::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
