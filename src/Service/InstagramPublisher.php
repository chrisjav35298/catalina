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
            'https://graph.instagram.com/' . rawurlencode($this->instagramUserId) . '/media',
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

        // 2. Publicar el contenedor.
        $respuestaPublicacion = $this->httpClient->request(
            'POST',
            'https://graph.instagram.com/' . rawurlencode($this->instagramUserId) . '/media_publish',
            [
                'body' => [
                    'creation_id' => $contenedor['id'],
                    'access_token' => $this->accessToken,
                ],
            ]
        );

        $resultado = $respuestaPublicacion->toArray(false);

        if ($respuestaPublicacion->getStatusCode() >= 400 || empty($resultado['id'])) {
            throw new \RuntimeException(
                $resultado['error']['message']
                    ?? 'Instagram no pudo publicar la imagen.'
            );
        }

        return $resultado;
    }
}
