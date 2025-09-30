<?php

namespace App\Form;

use App\Entity\Evenements;
use App\Entity\TypesEvenements;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvenementsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('ville')
            ->add('rue')
            ->add('cp')
            ->add('nb_rue')
            ->add('nb_places')
            ->add('nb_places_dispo')
            ->add('est_valide')
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
        ]);
    }
}
