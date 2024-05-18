<?php

namespace App\Form;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;



class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email address',
                 'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Please entre your e-mail.'
                        ])]
    

            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'New password',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please entre your password  .'
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
                    ]),]
            ])
            ->add('Confirmation',SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
