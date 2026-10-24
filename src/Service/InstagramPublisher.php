<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class InstagramPublisher
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $instagramUserId,
        private string $accessToken,
    ) {
    }

    public function publicarImagen(string $imagenUrl, string $texto): array
    {
        // 1. Crear el contenedor de la publicación.
        $respuesta = $this->httpClient->request(
            'POST',
            'https://graph.instagram.com/' .
                rawurlencode($this->instagramUserId) . '/media',
            [
                'body' => [
                    'image_url' => $imagenUrl,
                    'caption' => $texto,
                    'access_token' => $this->accessToken,
                ],
            ]
        );

        $contenedor = $respuesta->toArray(false);

        if ($respuesta->getStatusCode() >= 400 || empty($contenedor['id'])) {
            throw new \RuntimeException(
                $contenedor['error']['message']
                    ?? 'Instagram no pudo crear el contenedor de la imagen.'
            );
        }

        $creationId = $contenedor['id'];

        // 2. Esperar hasta que Instagram termine de procesar la imagen.
        $estadoFinalizado = false;

        for ($intento = 0; $intento < 12; $intento++) {
            sleep(3);

            $respuestaEstado = $this->httpClient->request(
                'GET',
                'https://graph.instagram.com/' .
                    rawurlencode($creationId),
                [
                    'query' => [
                        'fields' => 'status_code',
                        'access_token' => $this->accessToken,
                    ],
                ]
            );

            $estado = $respuestaEstado->toArray(false);

            if (
                $respuestaEstado->getStatusCode() >= 400
                || isset($estado['error'])
            ) {
                throw new \RuntimeException(
                    $estado['error']['message']
                        ?? 'No se pudo consultar el estado del contenedor de Instagram.'
                );
            }

            if (($estado['status_code'] ?? null) === 'FINISHED') {
                $estadoFinalizado = true;
                break;
            }

            if (in_array(
                $estado['status_code'] ?? null,
                ['ERROR', 'EXPIRED'],
                true
            )) {
                throw new \RuntimeException(
                    'Instagram no pudo procesar la imagen. Estado: ' .
                    $estado['status_code']
                );
            }
        }

        if (!$estadoFinalizado) {
            throw new \RuntimeException(
                'Instagram todavía no terminó de procesar la imagen. '
                . 'Intentá nuevamente más tarde.'
            );
        }

        // 3. Publicar únicamente cuando el contenedor esté listo.
        $respuestaPublicacion = $this->httpClient->request(
            'POST',
            'https://graph.instagram.com/' .
                rawurlencode($this->instagramUserId) . '/media_publish',
            [
                'body' => [
                    'creation_id' => $creationId,
                    'access_token' => $this->accessToken,
                ],
            ]
        );

        $resultado = $respuestaPublicacion->toArray(false);

        if (
            $respuestaPublicacion->getStatusCode() >= 400
            || empty($resultado['id'])
        ) {
            throw new \RuntimeException(
                $resultado['error']['message']
                    ?? 'Instagram no pudo publicar la imagen.'
            );
        }

        return $resultado;
    }
}
