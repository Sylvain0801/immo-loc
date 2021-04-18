<?php

namespace App\Form;

use App\Entity\Admin;
use App\Entity\Message;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('recipient', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user) {                    
                    return $user->getUserMailRole();
                },
                'attr' => ['class' => 'form-control'],
                'multiple' => true,
                'required' => false
            ])
            ->add('adminrecipient', EntityType::class, [
                'class' => Admin::class,
                'attr' => ['class' => 'form-control'],
                'multiple' => true,
                'required' => false
            ])
            ->add('subject', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('body', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 12,
                    'style' => 'resize:none'
                ],
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
