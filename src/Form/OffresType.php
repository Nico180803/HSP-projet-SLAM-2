<?php

namespace App\Form;

use App\Entity\ContactsEntreprise;
use App\Entity\Offres;
use App\Entity\TypesOffres;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OffresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('salaire')
            ->add('date_creation')
            ->add('date_fermeture')
            ->add('contactsEntreprises', EntityType::class, [
                'class' => ContactsEntreprise::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('refTypesOffre', EntityType::class, [
                'class' => TypesOffres::class,
                'choice_label' => 'libelle',
            ])
            ->add('refCreateur', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'nom',
            ])
            ->add('refUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'nom',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offres::class,
        ]);
    }
}
