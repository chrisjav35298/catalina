<?php

namespace App\Form;

use App\Entity\Actividades;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ActividadesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('titulo')
        ->add('descripcionCorta')
        ->add('descripcionLarga')
        ->add('imagenDestacada', FileType::class, [
            'label' => 'Imagen Destacada',
            'mapped' => false, // No está vinculado directamente a la entidad
            'required' => false, // O `false`, dependiendo de tu necesidad
        ])
        ->add('imagenes', CollectionType::class, [
            'entry_type' => ImagenType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => '',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Actividades::class,
        ]);
    }
}
