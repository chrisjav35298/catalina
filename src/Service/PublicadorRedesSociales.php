<?php

namespace App\Service;

use App\Entity\Actividades;

class PublicadorRedesSociales
{
    public function prepararPublicacion(Actividades $actividad): array
    {
        $texto = $actividad->getTitulo() . "\n\n"
            . $actividad->getDescripcionCorta();

        if ($actividad->getDescripcionLarga()) {
            $texto .= "\n\n" . $actividad->getDescripcionLarga();
        }

        $imagenUrl = null;

        if ($actividad->getImagenDestacada()) {
            $imagenUrl = 'https://fundacionstacatalina.wiz.com.ar/uploads/'
                . rawurlencode($actividad->getImagenDestacada());
        }

        return [
            'titulo' => $actividad->getTitulo(),
            'texto' => $texto,
            'imagen' => $imagenUrl,
            'fecha' => $actividad->getFecha()?->format('Y-m-d H:i:s'),
        ];
    }
}
