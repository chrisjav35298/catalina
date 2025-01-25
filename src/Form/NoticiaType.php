<?php

namespace App\Form;

use App\Entity\Noticia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class NoticiaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo')
            ->add('descripcionCorta')
            ->add('descripcionDetallada')
            ->add('imagen',FileType::class, [
                'label' => 'Imagen Destacada',
                'mapped' => false, // No está vinculado directamente a la entidad
                'required' => true, // O `false`, dependiendo de tu necesidad
            ])
            ->add('video')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Noticia::class,
        ]);
    }
}
