<?php

namespace App\Form;

use App\Entity\Comentarios;
use Symfony\Component\Form\AbstractType;
use Brokoskokoli\StarRatingBundle\Form\RatingType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComentariosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comentario')
            ->add('submit', SubmitType::class, [
                'label' => 'Enviar',
                'attr' => array('class'=>'mt-2 btn btn-warning'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comentarios::class,
        ]);
    }
}
