<?php

namespace App\Form;

use App\Entity\Configuracion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class ConfiguracionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ----------------------------------------------------
            // 1. Datos de Contacto Institucional
            // ----------------------------------------------------
            ->add('telefonoContacto', TelType::class, [
                'label' => 'Teléfono de contacto (Llamadas)',
                'required' => false,
                'attr' => [
                    'placeholder' => '+54 3624 855001'
                ],
                'help' => 'Se muestra en el encabezado, pie de página y bloque de donaciones.',
            ])
            ->add('emailContacto', EmailType::class, [
                'label' => 'Correo electrónico institucional',
                'required' => false,
                'attr' => [
                    'placeholder' => 'contacto@fundacionstacatalina.wiz.com.ar'
                ],
                'help' => 'Dirección de correo visible para el público.',
            ])

            // ----------------------------------------------------
            // 2. WhatsApp Flotante
            // ----------------------------------------------------
            ->add('whatsappNumero', TelType::class, [
                'label' => 'Número de WhatsApp',
                'required' => false,
                'attr' => [
                    'placeholder' => '+5493624725447'
                ],
                'help' => 'Sin espacios ni guiones. Incluir código de país (ej: +549...).',
            ])
            ->add('whatsappMensaje', TextareaType::class, [
                'label' => 'Mensaje predeterminado de WhatsApp',
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'placeholder' => 'Hola, quisiera más información sobre la fundación...'
                ],
                'help' => 'Texto predefinido que verá el usuario al abrir el chat.',
            ])

            // ----------------------------------------------------
            // 3. Datos Bancarios y Donaciones
            // ----------------------------------------------------
            ->add('bancoNombre', TextType::class, [
                'label' => 'Entidad bancaria o billetera virtual',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ej: Nuevo Banco del Chaco'
                ],
            ])
            ->add('cbu', TextType::class, [
                'label' => 'CBU / CVU (22 dígitos)',
                'required' => false,
                'attr' => [
                    'placeholder' => '3110015911000008491031',
                    'maxlength' => 22,
                ],
                'constraints' => [
                    new Length([
                        'min' => 22,
                        'max' => 22,
                        'exactMessage' => 'El CBU/CVU debe tener exactamente 22 números.',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'El CBU/CVU solo puede contener números.',
                    ]),
                ],
                'help' => 'Debe contener exactamente 22 números continuos.',
            ])
            ->add('aliasDonaciones', TextType::class, [
                'label' => 'Alias de la cuenta',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ej: FUNDACION.CATALINA'
                ],
                'help' => 'Alias bancario para transferencias o aportes.',
            ])
            ->add('cuit', TextType::class, [
                'label' => 'CUIT de la Fundación',
                'required' => false,
                'attr' => [
                    'placeholder' => '30-XXXXXXXX-X'
                ],
                'help' => 'Formato con guiones (ej: 30-71234567-8).',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Configuracion::class,
        ]);
    }
}