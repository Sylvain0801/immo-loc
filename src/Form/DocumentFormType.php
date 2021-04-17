<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocumentFormType extends AbstractType
{
    private $translator;

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
        $category = $this->translator->trans('--Category list--');
        $upload = $this->translator->trans('Upload file');

        $builder
            ->add('name', TextType::class, ['attr' => ['class' => 'form-control']])
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
                    'placeholder' => $category,
                    'attr' => ['class' => 'form-control']
                ])
            ->add('files', FileType::class,[
                'label' => $upload,
                'multiple' => false,
                'mapped' => false,
                'required' => true,
                'attr' => ['class' => 'form-control'],                
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
