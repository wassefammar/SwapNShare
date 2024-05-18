<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Categorie;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomCategorie', TypeTextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 25,
                        'maxMessage' => 'Categorie name cannot be longer than {{ limit }} characters',
                    ]),
                ],
                'attr' => [
                    'pattern' => '^\S+$',
                    'title' => 'Categorie name cannot contain spaces',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Service' => 'service',
                    'Product' => 'product',
                ],
                'expanded' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('save',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
