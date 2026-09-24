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
    #[Route(name: 'app_documento_institucional_index', methods: ['GET'])]
    public function index(DocumentoInstitucionalRepository $documentoInstitucionalRepository): Response
    {   
        if (
            !$this->isGranted('ROLE_ADMIN') &&
            !$this->isGranted('ROLE_DOCUMENTOS_INSTITUCIONALES')
        ) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('documento_institucional/index.html.twig', [
            'documento_institucionals' => $documentoInstitucionalRepository->findAll(),
        ]);
    }

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
                $destino = $projectDir . '/public/uploads/documentos';

                $archivoPdf->move($destino, $nuevoNombre);
                $documento->setArchivo($nuevoNombre);
            }

            $entityManager->persist($documento);
            $entityManager->flush();

            return $this->redirectToRoute('app_documento_institucional_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('documento_institucional/new.html.twig', [
            'documento_institucional' => $documento,
            'form' => $form,
        ]);
    }

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
                // Borra el archivo anterior en disco si existe
                if ($documento->getArchivo()) {
                    $rutaAnterior = $projectDir . '/public/uploads/documentos/' . $documento->getArchivo();
                    if (file_exists($rutaAnterior)) {
                        unlink($rutaAnterior);
                    }
                }

                $nuevoNombre = uniqid() . '.' . $archivoPdf->guessExtension();
                $destino = $projectDir . '/public/uploads/documentos';

                $archivoPdf->move($destino, $nuevoNombre);
                $documento->setArchivo($nuevoNombre);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_documento_institucional_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('documento_institucional/edit.html.twig', [
            'documento_institucional' => $documento,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_documento_institucional_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request, 
        DocumentoInstitucional $documentoInstitucional, 
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%')] string $projectDir
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $documentoInstitucional->getId(), $request->getPayload()->getString('_token'))) {
            // Elimina el archivo del disco al borrar el registro
            if ($documentoInstitucional->getArchivo()) {
                $rutaArchivo = $projectDir . '/public/uploads/documentos/' . $documentoInstitucional->getArchivo();
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
            }

            $entityManager->remove($documentoInstitucional);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_documento_institucional_index', [], Response::HTTP_SEE_OTHER);
    }
}