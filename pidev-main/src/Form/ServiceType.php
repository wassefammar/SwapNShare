<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreService',null, ['label'=>'Title', 'empty_data' => ''])
            ->add('descriptionService',null, ['label'=>'Description', 'empty_data' => ''])
            ->add('ville',null, ['label'=>'Address', 'empty_data' => ''])
            ->add('photo',FileType::class, [
                'data_class'=>null,
                'multiple'=> false,
                'required'=>true,
                'attr'=> array(
                     'accept' => 'image/*',
            )])
            ->add('choixEchange',null,['label'=>'Exchange choice', 'empty_data' => ''])
            ->add('categorie', EntityType::class, [
                'class' => 'App\Entity\Categorie', // Replace with the actual namespace of your Author entity
                'choice_label' => 'nomCategorie', // Assuming Author entity has a method getFullName() that returns the author's full name
                'placeholder' => 'Select a Categorie', // Optional, adds an empty option at the top
                'required' => true, // Set to true if the author selection is mandatory
    
            ])
            //->add('valid',null,['label'=>'valid', 'empty_data' => ''])
            ->add('Add_Service', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
