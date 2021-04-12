<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstname', TextType::class, [
            'attr' => ['class' => 'form-control'],
            ])
        ->add('lastname', TextType::class, [
            'attr' => ['class' => 'form-control'],
            ])
        ->add('email', EmailType::class,  [
            'attr' => ['class' => 'form-control'],
            'label' => 'Email'
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Tenant' => 'ROLE_TENANT', 
                    'Owner' => 'ROLE_OWNER',
                    'Lease owner' => 'ROLE_LEASEOWNER',
                    'Agent' => 'ROLE_AGENT'
                ],
                'expanded' => true,
                'multiple' => true,
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
