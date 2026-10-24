<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookPublisher
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $pageId,
        private string $accessToken,
    ) {
    }

    public function publicarImagen(string $imagenUrl, string $texto): array
    {
        $respuesta = $this->httpClient->request(
            'POST',
            'https://graph.facebook.com/v26.0/'
                . rawurlencode($this->pageId) . '/photos',
            [
                'body' => [
                    'url' => $imagenUrl,
                    'message' => $texto,
                    'published' => 'true',
                    'access_token' => $this->accessToken,
                ],
            ]
        );

        $resultado = $respuesta->toArray(false);

        if (
            $respuesta->getStatusCode() >= 400
            || empty($resultado['id'])
        ) {
            throw new \RuntimeException(
                $resultado['error']['message']
                    ?? 'Facebook no pudo publicar la imagen.'
            );
        }

        return $resultado;
    }
}
