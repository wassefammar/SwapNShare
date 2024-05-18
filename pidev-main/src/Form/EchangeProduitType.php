<?php

namespace App\Form;

use App\Entity\EchangeProduit;
use App\Entity\Produit;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Node\TextNode;

class EchangeProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $selectedProduct = $options['selectedProduct'];
        $userProducts = $options['userProducts'];
        
        $builder
            ->add('date_echange')
            ->add('produitIn', TextType::class, [
                'disabled' => true, // Set the produitIn field as read-only
                'data' => $selectedProduct->getTitreProduit(), // Set the data to the titreProduit of the first user product, adjust this according to your logic
                'mapped' => false, // This field is not mapped to the entity
            ])
            ->add('produitOut', EntityType::class, [
                'class' => Produit::class,
                'choices' => $userProducts,
                'choice_label' => 'titreProduit', 
                'placeholder' => 'Select the exchanged Product',
                'required' => true, 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EchangeProduit::class,
            'userProducts' => [], // Define default value for userProducts option
            'selectedProduct' => null, // Define default value for selectedProduct option
        ]);
    }
}
