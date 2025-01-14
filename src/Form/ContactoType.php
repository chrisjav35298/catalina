<?php

namespace App\Form;

use App\Form\Model\Contacto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo Electrónico',
                'required' => true,
            ])
            ->add('telefono', TelType::class, [
                'label' => 'Teléfono',
                'required' => false,
            ])
            ->add('mensaje', TextareaType::class, [
                'label' => 'Mensaje',
                'required' => true,
            ])
            // ->add('enviar', SubmitType::class, [
            //     'label' => 'Enviar',
            // ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contacto::class,
        ]);
    }
}
