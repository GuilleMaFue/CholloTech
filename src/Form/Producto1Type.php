<?php

namespace App\Form;

use App\Entity\Producto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextAreaType;

class Producto1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('link')
            ->add('descripcion')
            ->add('precio', TextType::class, [
                'attr' => array('class' => 'w-25')
            ])
            ->add('imagen', FileType::class, [
                'mapped' => false,
                'label' => "Sube una imagen",
                'required'    => !$options['update'],
                'attr' => array('class' => 'form-control'),
            ])
            ->add('categoria')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Producto::class,
        ]);
        $resolver->setRequired([
            'update',
        ]);
    }
}
