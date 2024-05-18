<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreProduit',null,['label'=>'title', 'empty_data' => ''])
            ->add('descriptionProduit',null,['label'=>'description', 'empty_data' => ''])
            ->add('photo', FileType::class, [
                'data_class'=>null,
                'multiple'=> false,
                'required'=>true,
                'empty_data' => '',
                'attr'=> array(
                     'accept' => 'image/*',
            )])
            ->add('ville',null,['label'=>'address', 'empty_data' => ''])
            ->add('choixEchange',null,['label'=>'Echange Choice', 'empty_data' => ''])
            ->add('etat',ChoiceType::class,[
                'choices'  => [
                    'New' => 'New',
                    'Hardly used' => 'Hardly used',
                    'Used' =>'Used',
                  ],
                'label'=>'state', 
                'empty_data' => '',
                ])
            ->add('prix',null,['label'=>'price', 'empty_data' => ''])
            ->add('categorie', EntityType::class, [
                'class' => 'App\Entity\Categorie', // Replace with the actual namespace of your Author entity
                'choice_label' => 'nomCategorie', // Assuming Author entity has a method getFullName() that returns the author's full name
                'placeholder' => 'Select a Categorie', // Optional, adds an empty option at the top
                'required' => true, // Set to true if the author selection is mandatory
    
            ])
           ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
