<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Validator\Constraints as Assert;

class InscriptionType extends AbstractType
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


            ->add('email',EmailType::class,[
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please entre your  e-mail.'
                    ]), 
                    new Assert\Email([
                        'message' => 'Please  e-mail valid.'
                    ]),    

                  
                ],
            ])
            
            ->add('mot_de_passe',RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer votre adresse mot de passe .'
                    ]),
                  
                    new Assert\Length([
                        'min' => 8,
                        'max' => 255, // ajustez selon vos besoins
                        'minMessage' => 'The password must contain at least {{ limit }} characters.',
                        'maxMessage' => 'The password cannot exceed {{ limit }} characters.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                        'message' => 'The password must contain at least one uppercase letter, one lowercase letter, one number and one symbol.',
                    ]),
                
                
                
                
                
                ],
                'invalid_message' => 'The passwords do not match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password '],
                'second_options' => ['label' => 'confirm password'],
                
                
                
                
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


                ->add('gender', ChoiceType::class, [
                    'label' => 'Gender',
                    'choices' => [
                        'Femme' => 'femme',
                        'Homme' => 'homme',
                    ],
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true,
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
