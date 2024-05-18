<?php

namespace App\Form;

use App\Entity\EchangeService;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EchangeServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $selectedService = $options['selectedService'];
        $userServices = $options['userServices'];

        $builder
            ->add('date_echange')
            ->add('serviceIn', TextType::class, [
                'disabled' => true, // Set the produitIn field as read-only
                'data' => $selectedService->getTitreService(), // Set the data to the titreProduit of the first user product, adjust this according to your logic
                'mapped' => false, // This field is not mapped to the entity
            ])
            ->add('serviceOut', EntityType::class, [
                'class' => Service::class, 
                'choices' => $userServices,
                'choice_label' => 'titreService', 
                'placeholder' => 'Select one of your Service',
                'required' => true, 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EchangeService::class,
            'selectedService'=> null,
            'userServices'=> [],
        ]);
    }
}
