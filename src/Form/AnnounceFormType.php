<?php

namespace App\Form;

use App\Entity\Announce;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class AnnounceFormType extends AbstractType
{

    private $userRepository;

    private $translator;

    public function __construct(UserRepository $userRepository, TranslatorInterface $translator)
    {
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $ownerList = $this->translator->trans('--Owner list--');
        $tenantList = $this->translator->trans('--Tenant list--');

        $builder
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control']
                ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 8,
                    'style' => 'resize:none']
                ])
            ->add('type', ChoiceType::class, [
                'choices' => ['home' => 'home', 'flat' => 'flat'],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('address', TextType::class, [
                'attr' => ['class' => 'form-control']
                ])
            ->add('city', TextType::class, [
                'attr' => ['class' => 'form-control']
                ])
            ->add('area', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 15,
                    'max' => 600],
                'required' => false
                ])
            ->add('price', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 150,
                    'max' => 6000],
                'required' => false
                ])
            ->add('rooms', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 20],
                'required' => false
                ])
            ->add('bedrooms', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 20],
                'required' => false
                ])
            ->add('active', CheckboxType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
                ])
            ->add('firstpage', CheckboxType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
                ])
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choices' => $this->userRepository->findUsersByRoleOwner(),
                'placeholder' => $ownerList,
                'required' => false
                ])
            ->add('tenant', EntityType::class, [
                'class' => User::class,
                'choices' => $this->userRepository->findUsersByRoleTenant(),
                'placeholder' => $tenantList,
                'required' => false
                ])
            ->add('images', FileType::class,[
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Announce::class,
        ]);
    }
}
