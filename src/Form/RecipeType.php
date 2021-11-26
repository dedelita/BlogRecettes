<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre',
                ]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Entrée' => "entree",
                    'Plat' => "plat",
                    'Dessert' => "dessert",
                    'Apéro' => "apero",
                    'Goûter' => "gouter"
                ],
            ])
            ->add('nbServings', IntegerType::class, [
                'label' => 'Personnes'
            ])
            ->add('difficulty', ChoiceType::class, [
                'label' => 'Difficulté',
                'choices' => [
                    'Facile' => 'facile',
                    'Intermédiaire' => 'intermediaire',
                    'Difficile' => 'difficile'
                ],
            ])
            ->add('preparation_time', TimeType::class, [
                'label' => 'Temps de préparation',
                'label_attr' => [
                    'class' => 'pt-0'
                ],
                'attr' => [
                    'class' => 'w-75'
                ]
            ])
            ->add('cooking_time', TimeType::class, [
                'label' => 'Temps de cuisson',
                'label_attr' => [
                    'class' => 'pt-0'
                ],
                'attr' => [
                    'class' => 'w-75'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/jpeg,image/png',
                ],
            ])
            ->add('ingredients', CollectionType::class, [
                'label' => 'Les ingrédients',
                'entry_type' => IngredientType::class,
                'allow_add' => true, // true si tu veux que l'utilisateur puisse en ajouter
                'allow_delete' => true, // true si tu veux que l'utilisateur puisse en supprimer
                'entry_options' => ['label' => false],
                'by_reference' => false, //En passant cet attribut à false, on force Symfony à appeler le setter de l’entité.
            ])
            ->add('steps', CollectionType::class, [
                'label' => 'Les étapes',
                'entry_type' => StepType::class,
                'allow_add' => true, // true si tu veux que l'utilisateur puisse en ajouter
                'allow_delete' => true, // true si tu veux que l'utilisateur puisse en supprimer
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
