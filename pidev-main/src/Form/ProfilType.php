<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,[
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter your last name'
                    ]), 
    
    
                ],
            ])
            ->add('prenom',TextType::class,[
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter your firstname.'
                    ]), 


                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'The first name must contain at least {{ limit }} characters.',
                       
                    ]),
                ],
            ])

            ->add('adresse', TextType::class,[
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter your address.'
                    ])  
                ],
            ])

            ->add('telephone',TextType::class, [
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Please enter a phone number.',
                        ]),
                        new Assert\Regex([
                            'pattern' => '/^\d+$/',
                            'message' => 'Please enter numbers only.',
                        ]),
                        new Assert\Length([
                            'min' => 8,
                            'max' => 8,
                            'exactMessage' => 'The length must be exactly 8 characters.',
                        ]),
                    ],]
                
                
                
                
                
                )
    
            ->add('email',EmailType::class,[
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer votre adresse e-mail.'
                    ]), 
                    new Assert\Email([
                        'message' => 'Veuillez entrer une adresse e-mail valide.'
                    ]),    

                  
                ],
            ])
          
           
        

            ->add('Submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
