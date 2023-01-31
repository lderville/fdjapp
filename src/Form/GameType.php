<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label'=>'Nom',
                'attr'=>[
                    'class'=>'form-control',
                    'maxlength'=>"50",
                    'autocomplete'=> "off",

                ]
            ])
            ->add('refBillingFdj', TextType::class,[
                'label'=>'Réference de facturation Fdj',
                'attr'=>[
                    'class'=>'form-control',
                    'maxlength'=>"50",
                    'autocomplete'=> "off"
                ]
            ])
            ->add('refDeliveryFdj', TextType::class,[
                'label'=>'Réference de livraison Fdj',
                'attr'=>[
                    'class'=>'form-control',
                    'maxlength'=>"50",
                    'autocomplete'=> "off"
                ]
            ])
            ->add('codeFdj',IntegerType::class,[
                'label'=>'Code du jeu',
                'attr'=>[ 'class'=>'form-control',
                    'min'=>1,
                    'autocomplete'=> "off"
                ]
            ])
            ->add('ticketNumber',IntegerType::class,[
                'label'=>'Nombre de ticket',
                'attr'=>[ 'class'=>'form-control',
                    'min'=>1,
                    'autocomplete'=> "off"
                ]
            ])
            ->add('ticketPrice',IntegerType::class,[
                'label'=>'Prix unitaire',
                'attr'=>[ 'class'=>'form-control',
                    'min'=>0,
                    'autocomplete'=> "off"
                ]
            ])
            ->add('isActivated', null,[
                'label'=>false,
                'attr'=>['type'=>'checkbox'
                ],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
