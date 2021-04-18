<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserRegistrationFormType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tenant = $this->translator->trans('Tenant');
        $owner = $this->translator->trans('Owner');
        $leaseowner = $this->translator->trans('Lease owner');
        $profil = $this->translator->trans('--Profile choice--');

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
                'attr' => ['class' => 'form-control'],
                'choices' => [
                    $tenant => 'Tenant',
                    $owner => 'Owner',
                    $leaseowner => 'Lease owner'
                ],
                'placeholder' => $profil,
                'expanded' => false,
                'multiple' => false,
                'mapped' => false,
                'required' => false
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Agree terms',
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
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
