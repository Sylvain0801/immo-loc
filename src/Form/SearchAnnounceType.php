<?php

namespace App\Form;

use App\Entity\Announce;
use App\Repository\AnnounceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchAnnounceType extends AbstractType
{
    private $announceRepository;

    private $translator;

    public function __construct(AnnounceRepository $announceRepository, TranslatorInterface $translator)
    {
        $this->announceRepository = $announceRepository;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $city = $this->translator->trans('--City--');
        $home = $this->translator->trans('home');
        $flat = $this->translator->trans('flat');

        $builder
        ->add('words', SearchType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Keyword'
                ],
                'required' => false
            ])
        ->add('type', ChoiceType::class, [
                'choices' => [ $home => 'home', $flat => 'flat'],
                'expanded' => false,
                'multiple' => false,
                'label' => false,
                'required' => false,
                'placeholder' => '--Type--',
                'attr' => [
                    'class' => 'cs-select cs-skin-border',
                ],
            ])
        ->add('city', EntityType::class, [
                'class' => Announce::class,
                'choices' =>  $this->announceRepository->findCities(),
                'choice_label' => 'city',
                'multiple' => false,
                'label' => false,
                'required' => false,
                'placeholder' => $city,
                'attr' => [
                    'class' => 'cs-select cs-skin-border',
                ],
            ])
        ->add('priceMin', ChoiceType::class, [
                'choices' => array_combine(range(100, 6000, 100), range(100, 6000, 100)),
                'attr' => [
                    'class' => 'cs-select cs-select-half cs-skin-border input-half', 
                    ],
                'placeholder' => 'Min',
                'required' => false,
                'label' => false
            ])
        ->add('priceMax', ChoiceType::class, [
                'choices' => array_combine(range(100, 6000, 100), range(100, 6000, 100)),
                'attr' => [
                    'class' => 'cs-select cs-select-half cs-skin-border input-half', 
                    ],
                'placeholder' => 'Max',
                'required' => false,
                'label' => false
            ])
        ->add('areaMin', ChoiceType::class, [
                'choices' => array_combine(range(20, 600, 10), range(20, 600, 10)),
                'attr' => [
                    'class' => 'cs-select cs-select-half cs-skin-border input-half', 
                    ],
                'placeholder' => 'Min',
                'required' => false,
                'label' => false
            ])
        ->add('areaMax', ChoiceType::class, [
                'choices' => array_combine(range(20, 600, 10), range(20, 600, 10)),
                'attr' => [
                    'class' => 'cs-select cs-select-half cs-skin-border input-half', 
                    ],
                'placeholder' => 'Max',
                'required' => false,
                'label' => false
            ])
        ->add('roomsMin', ChoiceType::class, [
                'choices' => array_combine(range(1, 20, 1), range(1, 20, 1)),
                'attr' => [
                    'class' => 'cs-select cs-select-half cs-skin-border input-half', 
                    ],
                'placeholder' => 'Min',
                'required' => false,
                'label' => false
            ])
        ->add('roomsMax', ChoiceType::class, [
                'choices' => array_combine(range(1, 20, 1), range(1, 20, 1)),
                'attr' => [
                    'class' => 'cs-select cs-select-half cs-skin-border input-half', 
                    ],
                'placeholder' => 'Max',
                'required' => false,
                'label' => false
            ])
        ->add('bedroomsMin', ChoiceType::class, [
                'choices' => array_combine(range(0, 20, 1), range(0, 20, 1)),
                'attr' => [
                    'class' => 'cs-select cs-select-half cs-skin-border input-half', 
                    ],
                'placeholder' => 'Min',
                'required' => false,
                'label' => false
            ])
        ->add('bedroomsMax', ChoiceType::class, [
                'choices' => array_combine(range(0, 20, 1), range(0, 20, 1)),
                'attr' => [
                    'class' => 'cs-select cs-select-half cs-skin-border input-half', 
                    ],
                'placeholder' => 'Max',
                'required' => false,
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
