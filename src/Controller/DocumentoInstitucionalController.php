<?php

namespace App\Controller;

use App\Entity\DocumentoInstitucional;
use App\Form\DocumentoInstitucionalType;
use App\Repository\DocumentoInstitucionalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/documento/institucional')]
final class DocumentoInstitucionalController extends AbstractController
{
    // Directorio privado donde se almacenan los documentos
    private const DIRECTORIO_DOCUMENTOS = '/var/documentos';

    /**
     * Listado administrativo de documentos.
     * Requiere autenticación y permisos.
     */
    #[Route(name: 'app_documento_institucional_index', methods: ['GET'])]
    public function index(
        DocumentoInstitucionalRepository $repository
    ): Response {

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            !$this->isGranted('ROLE_DOCUMENTOS_INSTITUCIONALES')
        ) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('documento_institucional/index.html.twig', [
            'documento_institucionals' => $repository->findAll(),
        ]);
    }

    /**
     * Crear documento.
     * Solamente administradores.
     */
    #[Route('/new', name: 'app_documento_institucional_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%')] string $projectDir
    ): Response {

        $documento = new DocumentoInstitucional();

        $form = $this->createForm(DocumentoInstitucionalType::class, $documento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile|null $archivoPdf */
            $archivoPdf = $form->get('archivoPdf')->getData();

            if ($archivoPdf) {

                $nuevoNombre = uniqid() . '.' . $archivoPdf->guessExtension();

                $destino = $projectDir . self::DIRECTORIO_DOCUMENTOS;

                if (!is_dir($destino)) {
                    mkdir($destino, 0775, true);
                }

                $archivoPdf->move($destino, $nuevoNombre);

                $documento->setArchivo($nuevoNombre);
            }

            $entityManager->persist($documento);
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_documento_institucional_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('documento_institucional/new.html.twig', [
            'documento_institucional' => $documento,
            'form' => $form,
        ]);
    }

    /**
     * Editar documento.
     * Solamente administradores.
     */
    #[Route('/{id}/edit', name: 'app_documento_institucional_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        DocumentoInstitucional $documento,
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%')] string $projectDir
    ): Response {

        $form = $this->createForm(DocumentoInstitucionalType::class, $documento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile|null $archivoPdf */
            $archivoPdf = $form->get('archivoPdf')->getData();

            if ($archivoPdf) {

                $destino = $projectDir . self::DIRECTORIO_DOCUMENTOS;

                if (!is_dir($destino)) {
                    mkdir($destino, 0775, true);
                }

                // Eliminar archivo anterior
                if ($documento->getArchivo()) {

                    $rutaAnterior = $destino . '/' . basename($documento->getArchivo());

                    if (is_file($rutaAnterior)) {
                        unlink($rutaAnterior);
                    }
                }

                // Guardar archivo nuevo
                $nuevoNombre = uniqid() . '.' . $archivoPdf->guessExtension();

                $archivoPdf->move($destino, $nuevoNombre);

                $documento->setArchivo($nuevoNombre);
            }

            $entityManager->flush();

            return $this->redirectToRoute(
                'app_documento_institucional_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('documento_institucional/edit.html.twig', [
            'documento_institucional' => $documento,
            'form' => $form,
        ]);
    }

    /**
     * Eliminar documento.
     * Solamente administradores.
     */
    #[Route('/{id}', name: 'app_documento_institucional_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request,
        DocumentoInstitucional $documentoInstitucional,
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%')] string $projectDir
    ): Response {

        if ($this->isCsrfTokenValid(
            'delete' . $documentoInstitucional->getId(),
            $request->getPayload()->getString('_token')
        )) {

            if ($documentoInstitucional->getArchivo()) {

                $rutaArchivo = $projectDir
                    . self::DIRECTORIO_DOCUMENTOS
                    . '/'
                    . basename($documentoInstitucional->getArchivo());

                if (is_file($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
            }

            $entityManager->remove($documentoInstitucional);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'app_documento_institucional_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }

    /**
     * Descargar documento.
     * Los públicos no requieren autenticación.
     * Los privados requieren permisos.
     */
    #[Route('/{id}/descargar', name: 'app_documento_institucional_descargar', methods: ['GET'])]
    public function descargar(
        DocumentoInstitucional $documento,
        #[Autowire('%kernel.project_dir%')] string $projectDir
    ): Response {

        // Verificar permisos para documentos privados
        if (!$documento->isPublico()) {

            if (
                !$this->isGranted('ROLE_ADMIN') &&
                !$this->isGranted('ROLE_DOCUMENTOS_INSTITUCIONALES')
            ) {
                throw $this->createAccessDeniedException();
            }
        }

        if (!$documento->getArchivo()) {
            throw $this->createNotFoundException('Documento no encontrado.');
        }

        $rutaArchivo = $projectDir
            . self::DIRECTORIO_DOCUMENTOS
            . '/'
            . basename($documento->getArchivo());

        if (!is_file($rutaArchivo)) {
            throw $this->createNotFoundException('Documento no encontrado.');
        }

        return $this->file(
            $rutaArchivo,
            $documento->getNombre() . '.pdf'
        );
    }

    /**
     * Listado público de documentos.
     * No requiere autenticación.
     */
    #[Route('/publicos', name: 'app_documento_institucional_publicos', methods: ['GET'])]
    public function publicos(
        DocumentoInstitucionalRepository $repository
    ): Response {

        return $this->render('documento_institucional/publicos.html.twig', [
            'documentos' => $repository->findBy(
                ['publico' => true],
                ['nombre' => 'ASC']
            ),
        ]);
    }
}
