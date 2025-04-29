<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Read the JSON file containing Bootstrap icons
        $icons = $this->getBootstrapIcons();

        $builder
            ->add('title')
            ->add('description')
            ->add('icon', ChoiceType::class, [
                'choices' => $icons,
                'choice_label' => function ($icon) {
                    return $icon; // Display the icon name as the label
                },
                'choice_value' => function ($icon) {
                    return $icon; // Use the icon name as the value
                },
                'placeholder' => 'Select an icon',
                'required' => true,
            ])
            ->add('fullDescription')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }

    /**
     * Reads the JSON file and returns an array of Bootstrap icons.
     */
    private function getBootstrapIcons(): array
    {
        $filePath = __DIR__ . '/../../config/bootstrap-icons.json'; // Adjust the path as needed

        if (!file_exists($filePath)) {
            throw new \RuntimeException('The bootstrap-icons.json file is missing.');
        }

        $jsonContent = file_get_contents($filePath);
        $icons = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to parse the bootstrap-icons.json file.');
        }

        return $icons;
    }
}