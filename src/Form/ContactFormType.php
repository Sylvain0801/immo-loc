<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => false
            ])
            ->add('body', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 8,
                    'style' => 'resize:none'],
                'label' => false
            ])
            ->add('firstname_sender', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => false
            ])
            ->add('lastname_sender', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => false
            ])
            ->add('sender', EmailType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
