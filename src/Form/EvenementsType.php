<?php

namespace App\Form;

use App\Entity\Evenements;
use App\Entity\TypesEvenements;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvenementsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userRoles = $options['user_roles'] ?? [];
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
                'disabled' => !$isAdminOrProf,
                'required' => false,
            ])
            ->add('refTypesEvenement', EntityType::class, [
                'class' => TypesEvenements::class,
                'choice_label' => 'id',
            ])
            ->add('responsables', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'mapped' => false,
                'label' => 'Responsables supplémentaires',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenements::class,
            'user_roles' => [],
        ]);
    }
}


