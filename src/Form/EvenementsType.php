<?php

namespace App\Form;

use App\Entity\Evenements;
use App\Entity\TypesEvenements;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EvenementsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userRoles = $options['user_roles'] ?? []; // récupère les rôles passés en option

        $isAdminOrProf = in_array('ROLE_ADMIN', $userRoles) || in_array('ROLE_PROF', $userRoles);

        $builder
            ->add('titre')
            ->add('description')
            ->add('ville')
            ->add('rue')
            ->add('cp')
            ->add('nb_rue')
            ->add('nb_places')
            ->add('nb_places_dispo')
            ->add('est_valide', CheckboxType::class, [
                'disabled' => !$isAdminOrProf, // désactive si pas admin ou prof
                'required' => false,
            ])
            ->add('refTypesEvenement', EntityType::class, [
                'class' => TypesEvenements::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenements::class,
            'user_roles' => [], // déclaration de l'option personnalisée
        ]);
    }
}

