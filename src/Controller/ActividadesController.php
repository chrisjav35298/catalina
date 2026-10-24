<?php

namespace App\Controller;
use App\Service\InstagramPublisher;
use App\Entity\Imagen;
use App\Entity\Actividades;
use App\Form\ActividadesType;
use App\Repository\ActividadesRepository;
use App\Service\PublicadorRedesSociales;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/actividades')]
#[IsGranted('ROLE_ADMIN')]
final class ActividadesController extends AbstractController
{
    #[Route(name: 'app_actividades_index', methods: ['GET'])]
    public function index(
        ActividadesRepository $actividadesRepository
    ): Response {
        return $this->render('actividades/index.html.twig', [
            'actividades' => $actividadesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_actividades_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        #[Autowire('%uploads_directory%')] string $uploadsDir
    ): Response {
        $actividade = new Actividades();

        $form = $this->createForm(ActividadesType::class, $actividade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Imagen destacada.
            $imagenDestacada = $form->get('imagenDestacada')->getData();

            if ($imagenDestacada instanceof UploadedFile) {
                $newFilenameDestacada = uniqid() . '.' .
                    $imagenDestacada->guessExtension();

                $imagenDestacada->move($uploadsDir, $newFilenameDestacada);

                $actividade->setImagenDestacada($newFilenameDestacada);
            }

            // Imágenes adicionales.
            foreach ($form->get('imagenes') as $imagenForm) {
                $imagenArchivo = $imagenForm->get('ruta')->getData();

                if ($imagenArchivo instanceof UploadedFile) {
                    $newFilename = uniqid() . '.' .
                        $imagenArchivo->guessExtension();

                    $imagenArchivo->move($uploadsDir, $newFilename);

                    $imagen = new Imagen();
                    $imagen->setRuta($newFilename);

                    $actividade->addImagen($imagen);
                    $entityManager->persist($imagen);
                }
            }

            $entityManager->persist($actividade);
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_actividades_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('actividades/new.html.twig', [
            'form' => $form->createView(),
            'actividade' => $actividade,
        ]);
    }

    #[Route('/{id}', name: 'app_actividades_show', methods: ['GET'])]
    public function show(Actividades $actividade): Response
    {
        return $this->render('actividades/show.html.twig', [
            'actividade' => $actividade,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_actividades_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Actividades $actividade,
        EntityManagerInterface $entityManager,
        #[Autowire('%uploads_directory%')] string $uploadsDir
    ): Response {
        $form = $this->createForm(ActividadesType::class, $actividade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Imagen destacada.
            $imagenDestacada = $form->get('imagenDestacada')->getData();

            if ($imagenDestacada instanceof UploadedFile) {
                $imagenAnterior = $actividade->getImagenDestacada();

                $newFilenameDestacada = uniqid() . '.' .
                    $imagenDestacada->guessExtension();

                $imagenDestacada->move($uploadsDir, $newFilenameDestacada);

                $actividade->setImagenDestacada($newFilenameDestacada);

                // Eliminar la imagen anterior después de guardar la nueva.
                if (
                    $imagenAnterior
                    && is_file($uploadsDir . '/' . $imagenAnterior)
                ) {
                    unlink($uploadsDir . '/' . $imagenAnterior);
                }
            }

            // Nuevas imágenes adicionales.
            foreach ($form->get('imagenes') as $imagenForm) {
                $imagenArchivo = $imagenForm->get('ruta')->getData();

                if ($imagenArchivo instanceof UploadedFile) {
                    $newFilename = uniqid() . '.' .
                        $imagenArchivo->guessExtension();

                    $imagenArchivo->move($uploadsDir, $newFilename);

                    $imagen = new Imagen();
                    $imagen->setRuta($newFilename);

                    $actividade->addImagen($imagen);
                    $entityManager->persist($imagen);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute(
                'app_actividades_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('actividades/edit.html.twig', [
            'actividade' => $actividade,
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/{id}/publicacion-preview',
        name: 'app_actividades_publicacion_preview',
        methods: ['GET']
    )]
    public function previewPublicacion(
        Actividades $actividade,
        PublicadorRedesSociales $publicadorRedesSociales
    ): Response {
        $datos = $publicadorRedesSociales->prepararPublicacion($actividade);

        return $this->json($datos);
    }

    #[Route(
        '/{id}/confirmar-publicacion',
        name: 'app_actividades_confirmar_publicacion',
        methods: ['GET', 'POST']
    )]
  
public function confirmarPublicacion(
    Request $request,
    Actividades $actividade,
    PublicadorRedesSociales $publicadorRedesSociales,
    InstagramPublisher $instagramPublisher
): Response {
    if ($request->isMethod('POST')) {
        if (!$this->isCsrfTokenValid(
            'publicar_' . $actividade->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Token CSRF inválido.'
            );
        }

        $datos = $publicadorRedesSociales->prepararPublicacion($actividade);

        if (empty($datos['imagen'])) {
            $this->addFlash(
                'danger',
                'No se puede publicar: la actividad no tiene una imagen destacada.'
            );

            return $this->redirectToRoute(
                'app_actividades_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        try {
            $resultado = $instagramPublisher->publicarImagen(
                $datos['imagen'],
                $datos['texto']
            );

            $this->addFlash(
                'success',
                'La publicación se envió correctamente a Instagram. ID: '
                . $resultado['id']
            );
        } catch (\Throwable $e) {
            $this->addFlash(
                'danger',
                'No se pudo publicar en Instagram: ' . $e->getMessage()
            );
        }

        return $this->redirectToRoute(
            'app_actividades_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }

    $datos = $publicadorRedesSociales->prepararPublicacion($actividade);

    return $this->render('actividades/confirmar_publicacion.html.twig', [
        'actividade' => $actividade,
        'datos' => $datos,
    ]);
}


    #[Route('/{id}', name: 'app_actividades_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Actividades $actividade,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $actividade->getId(),
            $request->getPayload()->getString('_token')
        )) {
            $entityManager->remove($actividade);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'app_actividades_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }



public function probarInstagram(): Response
{
    $userId = $_ENV['META_INSTAGRAM_USER_ID'] ?? null;
    $token = $_ENV['META_INSTAGRAM_ACCESS_TOKEN'] ?? null;

    if (!$userId || !$token) {
        return $this->json([
            'ok' => false,
            'mensaje' => 'Faltan las credenciales de Instagram.',
        ], 500);
    }

    $url = 'https://graph.instagram.com/'
        . rawurlencode($userId)
        . '?fields=id,username&access_token='
        . urlencode($token);

    $respuesta = file_get_contents($url);

    if ($respuesta === false) {
        return $this->json([
            'ok' => false,
            'mensaje' => 'No se pudo conectar con la API de Instagram.',
        ], 502);
    }

    $datos = json_decode($respuesta, true);

    if (isset($datos['error'])) {
        return $this->json([
            'ok' => false,
            'mensaje' => $datos['error']['message'] ?? 'Meta rechazó la solicitud.',
        ], 400);
    }

    return $this->json([
        'ok' => true,
        'instagram' => $datos,
    ]);
}

}
