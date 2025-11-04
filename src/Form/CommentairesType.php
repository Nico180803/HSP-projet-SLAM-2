<?php

namespace App\Form;

use App\Entity\Commentaires;
use App\Entity\Sujets;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentairesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reponse')
            ->add('pj')
            ->add('date_creation')
            ->add('refUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('refCommentaire', EntityType::class, [
                'class' => Commentaires::class,
                'choice_label' => 'id',
            ])
            ->add('refSujet', EntityType::class, [
                'class' => Sujets::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaires::class,
        ]);
    }
}
