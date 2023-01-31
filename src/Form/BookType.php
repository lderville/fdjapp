<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
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
            ->add('game', EntityType::class, [
                'class' => Game::class,
                'query_builder' => function (GameRepository $game) {
                    return $game->createQueryBuilder('g')
                        ->where('g.isActivated = true')
                        ->orderBy('g.name', 'ASC');
                },
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
