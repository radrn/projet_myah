<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\User;
use Faker\Core\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "What's new?",
                    'class' => 'content',
                    'rows' => 4
                ]
            ])
            ->add('image', FileType::class, [
                'label' => '',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'input'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Poster',
                'attr' => ['class' => 'post-btn']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
