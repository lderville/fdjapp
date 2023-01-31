<?php

namespace App\Form;

use App\Entity\Delivery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewDeliveryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('deliveryDate',DateType::class,[
                'label'=>'Date de livraison',
                'attr'=>['class'=>'form-control w-50'],
                'widget' => 'single_text',
                'required'=>true,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Delivery::class,
        ]);
    }
}
