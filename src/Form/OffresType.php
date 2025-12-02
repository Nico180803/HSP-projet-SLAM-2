<?php

namespace App\Form;

use App\Entity\ContactsEntreprise;
use App\Entity\Offres;
use App\Entity\TypesOffres;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OffresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $contactsChoices = null;
        $user = $options['user'];
        if ($user){
            if (in_array('ROLE_ENTREPRISE', $user->getRoles())) {
                $contactsChoices = $user->getContactsEntreprises();
            }
        }
        $builder
            ->add('titre')
            ->add('description')
            ->add('salaire')
            ->add('date_creation')
            ->add('date_fermeture')
            ->add('contactsEntreprises', EntityType::class, [
                'class' => ContactsEntreprise::class,
                'choice_label' => function (ContactsEntreprise $c) {
                    return sprintf('%s %s (%s)', $c->getPrenom(), $c->getNom(), $c->getFonction());
                },
                'multiple'    => true,
                'expanded'    => true,
                'by_reference'=> false,
                'label'       => 'Contact entreprise',
                'choices'      => $contactsChoices,
            ])
            ->add('refTypesOffre', EntityType::class, [
                'class' => TypesOffres::class,
                'choice_label' => 'libelle',
                'label' => "Type d'offre",
            ]);
            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $offre = $event->getData();

                $dateCreation = $offre->getDateCreation();
                $dateFermeture = $offre->getDateFermeture();

                if ($dateCreation && $dateFermeture && $dateFermeture <= $dateCreation) {
                    $form->get('date_fermeture')->addError(
                        new FormError('La date de fermeture doit être supérieure à la date de création.')
                    );
                }
            });

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offres::class,
            'user'       => null,
        ]);
    }
}
