<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Product name field
            ->add('name', TextType::class, [
                'label' => 'Product Name',
            ])
            // Category field as a choice field using the constants from Product entity
            ->add('category', ChoiceType::class, [
                'choices'  => array_combine(Product::getCategories(), Product::getCategories()),
                'label'    => 'Category',
            ])
            // Brand field (optional)
            ->add('brand', TextType::class, [
                'label'    => 'Brand',
                'required' => false,
            ])
            // Model field (optional)
            ->add('model', TextType::class, [
                'label'    => 'Model',
                'required' => false,
            ])
            // Description field (optional)
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
            ])
            // Price field, using MoneyType to handle decimal values
            ->add('price', MoneyType::class, [
                'currency' => 'USD', // Adjust the currency if needed
                'label'    => 'Price',
            ])
            // Stock Quantity field
            ->add('stockQuantity', IntegerType::class, [
                'label' => 'Stock Quantity',
            ])
            // Image URL field (optional)
            ->add('imageUrl', TextType::class, [
                'label'    => 'Image URL',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Bind the form to the Product entity
            'data_class' => Product::class,
        ]);
    }
}
