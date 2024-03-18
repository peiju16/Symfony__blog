<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Comment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('firstName',  TextType::class, [
            //     'constraints' => [
            //         new NotBlank([
            //             'message' => 'Please enter your firstname',
            //         ]),
            //     ],
            // ])
            ->add('content', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your firstname',
                    ]),
                ],
            ])
            // ->add('article', EntityType::class, [
            //     'class' => Article::class,
            //     'choice_label' => 'id',
            // ])
            ->add('save', SubmitType::class, [
                'label' => 'Send',
                
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
