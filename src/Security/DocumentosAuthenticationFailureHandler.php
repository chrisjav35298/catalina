<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class DocumentosAuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): Response {
        // Si el login viene desde el modal de Documentación
        if ($request->request->get('_login_context') === 'documentos') {

            $returnUrl = $request->request->get('_return_url');

            if ($returnUrl) {
                $separator = str_contains($returnUrl, '?') ? '&' : '?';

                return new RedirectResponse(
                    $returnUrl . $separator . 'documentos_login_error=1'
                );
            }
        }

        // Login normal: comportamiento habitual
        return new RedirectResponse(
            $this->urlGenerator->generate('app_login')
        );
    }
}