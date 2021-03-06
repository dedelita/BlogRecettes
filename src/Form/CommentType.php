<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('writer', TextType::class, [
                'label' => 'Votre nom ou pseudo *',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'attr' => [
                    'class' => 'mb-1'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email *',
                'help' => 'Ne sera pas affiché',
                'label_attr' => [
                    'class' => 'col-form-label p-0'
                ],
                'help_attr' => [
                    'class' => 'mt-0 fst-italic'
                ],
                'attr' => [
                    'class' => 'mb-1'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Entrez votre commentaire.',
                    'rows' => 5
                ]
            ])
            ->add('stars', HiddenType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'rating-input',
                    'min' => 0,
                    'max' => 5
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'attr' => ['id' => 'addComment']
        ]);
    }
}
