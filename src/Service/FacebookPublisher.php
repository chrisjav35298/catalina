<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookPublisher
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $pageId,
        private readonly string $accessToken,
        private readonly string $apiVersion = 'v20.0', // Versión válida de la Graph API
    ) {
    }

    /**
     * @throws \RuntimeException
     */
    public function publicarImagen(string $imagenUrl, string $texto): array
    {
        $endpoint = sprintf(
            'https://graph.facebook.com/%s/%s/photos',
            $this->apiVersion,
            rawurlencode($this->pageId)
        );

        try {
            $respuesta = $this->httpClient->request('POST', $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
                'body' => [
                    'url' => $imagenUrl,
                    'message' => $texto,
                    'published' => 'true',
                ],
            ]);

            $statusCode = $respuesta->getStatusCode();
            $resultado = $respuesta->toArray(false);
        } catch (ExceptionInterface $e) {
            throw new \RuntimeException(
                'Error de conexión con la Graph API de Facebook: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        if ($statusCode >= 400 || empty($resultado['id'])) {
            $mensajeError = $resultado['error']['message'] 
                ?? 'Facebook no pudo publicar la imagen.';
            
            $codigoError = $resultado['error']['code'] ?? $statusCode;

            throw new \RuntimeException(
                sprintf('Error de Facebook [%s]: %s', $codigoError, $mensajeError)
            );
        }

        return $resultado;
    }
}