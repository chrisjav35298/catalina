<?php

namespace App\Form;

use App\Entity\Ley;
use App\Entity\Articulo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class LeyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo')
            ->add('descripcion')
            ->add('fechaSancion', null, [
                'widget' => 'single_text',
            ])
            ->add('fechaPromulgacion', null, [
                'widget' => 'single_text',
            ])
            ->add('activo', CheckboxType::class, [
                'required' => false, // No es obligatorio
                'label' => '¿Está activo?', // Etiqueta del checkbox
                'attr' => [
                    'class' => 'form-check-input', // Clases CSS opcionales
                ],
            ])
            
            ->add('articulos', CollectionType::class, [
                'label' => '-  ',
                'entry_type' => ArticuloType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ley::class,
        ]);
    }
}
