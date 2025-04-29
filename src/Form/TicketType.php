<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Subject',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a subject',
                    ]),
                ],
            ])
            ->add('category', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-select',
                ],
                'choices' => [
                    'Technical Support' => 'technical_support',
                    'Billing Inquiry' => 'billing_inquiry',
                    'Product Information' => 'product_information',
                    'Order Status' => 'order_status',
                    'Return/Refund' => 'return_refund',
                    'Other' => 'other',
                ],
                'placeholder' => 'Select a category',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a category',
                    ]),
                ],
            ])
            ->add('priority', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-select',
                ],
                'choices' => Ticket::getPriorityChoices(),
                'placeholder' => 'Select priority',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a priority',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Describe your issue in detail',
                    'rows' => 5,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please provide a description',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
