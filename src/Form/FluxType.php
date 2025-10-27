<?php

namespace App\Form;

use App\Entity\Flux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FluxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Tout le monde' => 'ROLE_USER',
                    'Élève' => 'ROLE_ELEVE',
                    'Médecin' => 'ROLE_MEDECIN',

                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôles autorisés',
                'required' => true,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Flux::class,
        ]);
    }
}
