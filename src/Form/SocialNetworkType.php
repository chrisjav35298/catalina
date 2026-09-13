<?php

namespace App\Form;

use App\Entity\SocialNetwork;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialNetworkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre de la red',
                'attr' => [
                    'placeholder' => 'Ej: Instagram, Facebook, Red Nueva'
                ],
            ])
            ->add('url', UrlType::class, [
                'label' => 'Enlace al perfil (URL)',
                'attr' => [
                    'placeholder' => 'https://www.instagram.com/fundacion_stacatalina'
                ],
            ])
            ->add('icono', TextType::class, [
                'label' => 'Clase del Ícono (Font Awesome)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'fab fa-instagram o fas fa-share-alt'
                ],
                'help' => 'Copiá la clase desde fontawesome.com. Ejemplos: "fab fa-instagram", "fas fa-share-alt"',
            ])
            ->add('ordenVisualizacion', IntegerType::class, [
                'label' => 'Orden de prioridad',
                'required' => false,
                'empty_data' => '0',
                'help' => 'Menor número aparece primero (ej: 0, 1, 2)',
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => '¿Mostrar en el sitio?',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialNetwork::class,
        ]);
    }
}