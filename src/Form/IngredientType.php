<?php

namespace App\Form;

use App\Entity\Ingredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number', IntegerType::class, [
                "label"=> false,
                "attr" => [
                    "min" => 1,
                ]
            ])
            ->add('type', TextType::class, [
                "label"=> false,
                "required" => false,
                "attr" => [
                    "placeholder" => "g, l, sachet, pincée, etc."
                ]
            ])
            ->add('name', TextType::class, [
                "label"=> false,
                "attr" => [
                    "placeholder" => "farine, huile, sel, etc."
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
            'attr' => ['class' => 'form-inline']
        ]);
    }
}
