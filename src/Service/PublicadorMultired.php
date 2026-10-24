<?php

namespace App\Service;

class PublicadorMultired
{
    public function __construct(
        private FacebookPublisher $facebookPublisher,
        private InstagramPublisher $instagramPublisher,
    ) {
    }

    public function publicar(
        array $redes,
        string $imagenUrl,
        string $texto
    ): array {
        $resultados = [];

        foreach (array_unique($redes) as $red) {
            try {
                if ($red === 'facebook') {
                    $resultado = $this->facebookPublisher->publicarImagen(
                        $imagenUrl,
                        $texto
                    );
                } elseif ($red === 'instagram') {
                    $resultado = $this->instagramPublisher->publicarImagen(
                        $imagenUrl,
                        $texto
                    );
                } else {
                    $resultados[$red] = [
                        'ok' => false,
                        'error' => 'Red social no válida.',
                    ];

                    continue;
                }

                $resultados[$red] = [
                    'ok' => true,
                    'id' => $resultado['id'] ?? null,
                ];
            } catch (\Throwable $e) {
                $resultados[$red] = [
                    'ok' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $resultados;
    }
}
