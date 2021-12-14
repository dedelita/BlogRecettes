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

class ReplyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('writer', TextType::class, [
            'label' => false,
            'label_attr' => [
                'class' => 'col-form-label'
            ],
            'attr' => [
                'class' => 'mb-1',
                'placeholder' => 'Votre nom ou pseudo',
            ]
        ])
        ->add('email', EmailType::class, [
            'label' => false,
            'label_attr' => [
                'class' => 'col-form-label'
            ],
            'attr' => [
                'class' => 'mb-1',
                'placeholder' => 'Votre email',
                'resize' => false
            ]
        ])
        ->add('content', TextareaType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Entrez votre réponse.',
                "rows" => 5
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
