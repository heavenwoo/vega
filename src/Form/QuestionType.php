<?php

namespace Vega\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vega\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vega\Form\Type\TagsInputType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.subject'
            ])
            ->add('content', TextareaType::class, [
                'attr' => [],
                'label' => 'label.content'
            ])
            ->add('tags', TagsInputType::class, [
                'mapped' => false,
                'label' => 'label.tags'
            ])
            ->add('label.submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // uncomment if you want to bind to a class
            'data_class' => Question::class,
        ]);
    }
}
