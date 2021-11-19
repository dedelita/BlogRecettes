<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => false,
                "attr" => [
                    "placeholder" => "Titre",
                    "class" => "w-50"
                ]])
            ->add('type', ChoiceType::class, [
                "label" => false,
                'choices' => [
                    'entrée' => "entree",
                    'plat' => "plat",
                    'dessert' => "dessert",
                    'apéro' => "apero",
                    'goûter' => "gouter"
                ],
                "attr" => [
                    "class" => "w-25"
                ]
            ])
            ->add('image', FileType::class, [
                "label" => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/jpeg,image/png',
                ],
            ])
            ->add('ingredients', CollectionType::class, [
                'entry_type' => IngredientType::class,
                'allow_add' => true, // true si tu veux que l'utilisateur puisse en ajouter
                'allow_delete' => true, // true si tu veux que l'utilisateur puisse en supprimer
                'label' => 'Les ingrédients',
                'entry_options' => ['label' => false],
                'by_reference' => false, //En passant cet attribut à false, on force Symfony à appeler le setter de l’entité.
            ])
            ->add('steps', CollectionType::class, [
                'entry_type' => StepType::class,
                'allow_add' => true, // true si tu veux que l'utilisateur puisse en ajouter
                'allow_delete' => true, // true si tu veux que l'utilisateur puisse en supprimer
                'label' => 'Les étapes',
                'entry_options' => ['label' => false],
                'prototype' => true, //prototype : On veut qu’un prototype soit défini afin de pouvoir gérer la collection en javascript côté client.
                'by_reference' => false, //En passant cet attribut à false, on force Symfony à appeler le setter de l’entité.
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'attr' => [
                'class' => 'p-5'
            ]
        ]);
    }
}
