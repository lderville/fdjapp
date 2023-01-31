<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, [
                'label'=>'Référence',
                'attr'=>[
                    'class'=>'form-control',
                    'maxlength'=>"80",
                    'autocomplete'=> "off",

                ]
            ])
            ->add('billingDate', DateType::class,[
                'label'=>'Date de facturation',
                'attr'=>['class'=>'form-control w-50'],
                'widget' => 'single_text',
                'required'=>false,
            ])
            ->add('activationDate',DateType::class,[
        'label'=>'Date d\'activation',
        'attr'=>['class'=>'form-control w-50'],
        'widget' => 'single_text',
        'required'=>false,
    ])
            ->add('game',null, [
                'label'=>'Choix du jeu',
                'attr'=>['class'=>'selectpicker mx-4',
                    'data-live-search'=>"true",
                    'onchange'=>'getGameValue()']
            ])
            ->add('repayment', IntegerType::class, [
                'required'=>false,
                'label'=>'Remboursement',
                'attr'=>[
                    'class'=>'form-control w-75',
                    'min'=>"1",
                    'autocomplete'=> "off",

                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
