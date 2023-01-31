<?php

namespace App\Form;

use Smalot\PdfParser\XObject\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\ImageValidator;

class CheckDeliveryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newDelivery', FileType::class,[
                'label'=>false,
                'attr'=>['class'=>'form-control w-50'],
                'constraints' => [
                    new File([
                        'maxSize' => '4M',
                        'mimeTypes' => [
                            'text/csv',

                        ],
                        'maxSizeMessage'=>'Le fichier CSV est trop grande ( + 4 Mo )',
                        'mimeTypesMessage' => 'Ce n\'est pas un fichier CSV valide',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
