<?php

namespace App\Form;

use App\Entity\AboutUs;
use App\Form\DataTransformer\JsonToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AboutUsType extends AbstractType
{ 


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('features', TextareaType::class, [
                'attr' => ['rows' => 5] // Optional: improve textarea appearance
            ])
            ->add('contactNumber')
            ->add('image')
            ->add('Email')
            ->add('Address')
            ->add('links', TextareaType::class, [
                'attr' => ['rows' => 5]
            ]);
        ;
        $builder->get('features')->addModelTransformer(new JsonToArrayTransformer());
        $builder->get('links')->addModelTransformer(new JsonToArrayTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AboutUs::class,
        ]);
    }
}
