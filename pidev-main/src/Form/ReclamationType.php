<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('titreR', ChoiceType::class, [
                'label' => 'Reason for Complaint', 'empty_data' => '',
                'placeholder' => 'Select a Reason',
                'choices' => [
                    'Poor Service' => 'Poor Service',
                    'Product Defects' => 'Product Defects',
                    'Billing and Pricing Disputes' => 'Billing and Pricing Disputes',
                    'Misrepresentation or False Advertising' => 'Misrepresentation or False Advertising',
                    'Employee Misconduct' => 'Employee Misconduct',
                    'Non-Delivery of Items' => 'Non-Delivery of Items',
                    'Payment Issues' => 'Payment Issues',
                    'Seller Misconduct' => 'Seller Misconduct',
                    'Disputes Over Returns and Refunds' => 'Disputes Over Returns and Refunds:',
                    'Communication Problems' => 'Communication Problems',
                    'Shipping and Delivery Issues' => 'Shipping and Delivery Issues',
                    'PQuality Assurance' => 'Quality Assurance',
                    'Privacy and Security Concerns' => 'Privacy and Security Concerns',
                    'Transaction Disputes' => 'Transaction Disputes',
                    'other' => 'other',

                ],
            ])
            ->add('urgence', ChoiceType::class, [
                'label' => 'Urgency Level', 'empty_data' => '',
                'placeholder' => 'Select a level',
                'choices' => [
                    'Low' => 'Low',
                    'Normal' => 'Normal',
                    'High' => 'High',
                    'Urgent' => 'Urgent',
                    'Critical' => 'Critical',

                ],
            ])
            ->add('descriptionR', null, [
                'label' => 'description',
                'empty_data' => ''
            ])
            ->add('date')

            ->add('Save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
