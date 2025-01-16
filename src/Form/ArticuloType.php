<?php

namespace App\Form;

use App\Entity\Articulo;
use App\Entity\Ley;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticuloType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('numero', null, [
            'attr' => ['class' => 'form-control mb-3', 'style' => 'max-width: 500px;'],
        ])
        ->add('contenido', null, [
            'attr' => ['class' => 'form-control mb-3'],
        ]);
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articulo::class,
        ]);
    }
}
