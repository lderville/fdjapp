<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArchiveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, [
                'label'=>'Référence',
                'required'=>false,
                'attr'=>[
                    'class'=>'form-control',
                    'maxlength'=>"50",
                    'autocomplete'=> "off",

                ]
            ])
            ->add('isActivated', null,[
                'label'=> 'Activé',
                'required'=>false,

            ])
            ->add('isNotActivated', CheckboxType::class,[
                'mapped'=>false,
                'label'=> 'Non activé',
                'required'=>false,
            ])
            ->add('isNotBilled',CheckboxType::class,[
                'mapped'=>false,
                'label'=>'Non Facturé',
                'required'=>false,
            ])
            ->add('isBilled',null,[
                'label'=>'Facturé',
                'required'=>false,
            ])
            ->add('modificationdate',DateType::class,[
                'mapped'=>false,
                'label'=>'Date de dernière modification',
                'attr'=>['class'=>'form-control w-75'],
                'widget' => 'single_text',
                'required'=>false,
            ])

            ->add('game', EntityType::class, [
                'class' => Game::class,
                'required'=>false,
                'query_builder' => function (GameRepository $game) {
                    return $game->createQueryBuilder('g')
                        ->orderBy('g.name', 'ASC');
                },
                'placeholder' => 'Choisir un jeu',
                'label'=>'Choix du jeu',
                'attr'=>['class'=>'selectpicker mx-4',
                    'data-live-search'=>"true",
                    'onchange'=>'getGameValue()']
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
