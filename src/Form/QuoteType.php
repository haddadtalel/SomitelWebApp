<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Quote;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Load IT positions from CSV
        $positions = $this->getITPositions();
        
        // Predefined services
        $services = [
            'VoIP Installation' => 'VoIP Installation',
            'ISDN Systems' => 'ISDN Systems',
            'Network Maintenance' => 'Network Maintenance',
            'IT System Diagnostics and Optimization.' => 'IT System Diagnostics and Optimization.',
            'Fire Safety' => 'Fire Safety',
            'Building Security'=>'Building Security',
        ];

        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control bg-light border-0',
                    'placeholder' => 'First Name',
                    'style' => 'height: 55px;',
                ],
                'required' => true,
            ])
            ->add('LastName', TextType::class, [
                'attr' => [
                    'class' => 'form-control bg-light border-0',
                    'placeholder' => 'Last Name',
                    'style' => 'height: 55px;',
                ],
                'required' => true,
            ])
            ->add('Company', TextType::class, [
                'attr' => [
                    'class' => 'form-control bg-light border-0',
                    'placeholder' => 'Company Name',
                    'style' => 'height: 55px;',
                ],
                'required' => true,
            ])
            ->add('Position', ChoiceType::class, [
                'choices' => array_combine($positions, $positions),
                'placeholder' => 'Select A Position',
                'attr' => [
                    'class' => 'form-select bg-light border-0',
                    'style' => 'max-height: 200px; overflow-y: auto;',
                ],
                'required' => true,
            ])
            ->add('Phone', TelType::class, [
                'attr' => [
                    'class' => 'form-control bg-light border-0',
                    'placeholder' => 'Your Phone (+XXX)',
                    'style' => 'height: 55px;',
                ],
                'required' => true,
            ])
            ->add('Service', ChoiceType::class, [
                'choices' => $services,
                'placeholder' => 'Select A Service',
                'attr' => [
                    'class' => 'form-select bg-light border-0',
                    'style' => 'max-height: 200px; overflow-y: auto;',
                ],
                'required' => true,
            ])
            ->add('Message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control bg-light border-0',
                    'placeholder' => 'Message',
                    'rows' => 3,
                ],
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Request a Quote',
                'attr' => [
                    'class' => 'btn btn-dark w-100 py-3',
                ],
            ]);
    }

    private function getITPositions(): array
    {
        $filePath = __DIR__ . '/../../public/uploads/it_positions_dataset.csv';
        $positions = [];

        if (($file = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($file, 1000, ',')) !== false) {
                $positions[] = $data[1]; // Assuming first column is position name
            }
            fclose($file);
        }

        return $positions;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
        ]);
    }
}

