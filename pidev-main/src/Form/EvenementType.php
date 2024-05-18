<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreEvenement')
            ->add('descriptionEvenement')
            ->add('dateDebut')
            ->add('dateFin')
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'choices' => $options['userProducts'], // Pass the user's products as choices
                'choice_label' => 'titreProduit', // Adjust 'nomProduit' to the property in Produit you want to show in the dropdown
            ])

            ->add('participationEvenement')
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'Waitlisted' => 'Waitlisted',
                ],
                'attr' => ['class' => 'form-select d-none'], // Use Bootstrap's d-none class to hide
                'label_attr' => ['class' => 'form-label d-none'],
                'disabled' => true, // This will prevent the field from being changed by the user
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
            'userProducts' => null, // Set a default value for userProducts
        ]);
    }
}
