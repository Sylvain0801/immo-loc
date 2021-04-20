<?php

namespace App\Form;

use App\Entity\Document;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class TransfertDocFormType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $insurance = $this->translator->trans('Insurance');
        $contract = $this->translator->trans('Contract');
        $lease = $this->translator->trans('Lease');
        $rent = $this->translator->trans('Rent receipt');
        $other = $this->translator->trans('Other');
    
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    ]
                ])
            ->add('category', ChoiceType::class, [
                'choices' => [ 
                    $insurance => 'Insurance',
                    $contract => 'Contract',
                    $lease => 'Lease',
                    $rent => 'Rent receipt',
                    $other => 'Other'
                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-control'
                    ]
                ])
            ->add('recipient', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'mapped' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control'
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
