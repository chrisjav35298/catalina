<?php

namespace App\Form;

use App\Entity\CarruselRefugio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CarruselRefugioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo')
            ->add('subtitulo')
            ->add('link')
            ->add('isActive', CheckboxType::class, [
                'required' => false, // No es obligatorio
                'label' => '¿Está activo?', // Etiqueta del checkbox
                'attr' => [
                    'class' => 'form-check-input', // Clases CSS opcionales
                ],
            ])
            ->add('imagen',FileType::class, [
                'label' => 'Imagen Destacada',
                'mapped' => false, // No está vinculado directamente a la entidad
                'required' => true, // O `false`, dependiendo de tu necesidad
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarruselRefugio::class,
        ]);
    }
}
