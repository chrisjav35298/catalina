<?php

namespace App\Form;

use App\Entity\Comunidad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ComunidadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo')
            ->add('descripcion')
            ->add('videoUrl', UrlType::class, [
                'label' => 'Enlace del video',
                'help'  => 'Pegá el enlace completo del video (ej: https://www.facebook.com/.../videos/...) o de esta forma
                https://www.instagram.com/reel/DTSk44IEZLp/'
            ])
            ->add('plataforma', ChoiceType::class, [
                'choices' => [
                    'Facebook' => 'facebook',
                    'Instagram' => 'instagram',
                ],
                'expanded' => false, // select
                'multiple' => false,
                'label' => 'Plataforma del video',
            ])
            ->add('activo')
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comunidad::class,
        ]);
    }
}
