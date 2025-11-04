<?php

namespace App\Form;

use App\Entity\Flux;
use App\Entity\Sujets;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SujetsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message')
            ->add('pj')
            ->add('date_creation')
            ->add('titre')
            ->add('refFlux', EntityType::class, [
                'class' => Flux::class,
                'choice_label' => 'id',
            ])
            ->add('refUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sujets::class,
        ]);
    }
}
