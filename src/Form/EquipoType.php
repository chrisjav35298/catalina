<?php

namespace App\Form;

use App\Entity\Equipo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EquipoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('staff', ChoiceType::class, [
                'choices'  => [
                    'Comisión directiva' => 'comisionDirectiva',
                    'Equipo legal' => 'equipoLegal',
                    'Colaboradores' => 'colaboradores',
                ],
                'label' => 'Staff', // Si deseas un label para el campo
                'placeholder' => 'Seleccionar...', // Opcional, para mostrar la opción "Selecciona..."
                'required' => true,  // Define si es obligatorio
            ])
            ->add('puesto', ChoiceType::class, [
                'choices'  => [
                    'Presidente' => 'presidente',
                    'Secretario/a' => 'secretario/a',
                    'Tesorero' => 'tesorero',
                    'Área civil' => 'areaCivil',
                    'Área penal' => 'areaPenal',
                    'Colaborador' => 'colaborador/a',
                ],
                'label' => 'Puesto', // Si deseas un label para el campo
                'placeholder' => 'Seleccionar...', // Opcional, para mostrar la opción "Selecciona..."
                'required' => true,  // Define si es obligatorio
            ])
            ->add('imagen',FileType::class, [
                'label' => 'Imagen',
                'mapped' => false, // No está vinculado directamente a la entidad
                'required' => false, // O `false`, dependiendo de tu necesidad
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipo::class,
        ]);
    }
}
