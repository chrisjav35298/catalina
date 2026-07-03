<?php

namespace App\Controller;

use App\Entity\Comunidad;
use App\Form\ComunidadType;
use App\Repository\ComunidadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/comunidad')]
final class ComunidadController extends AbstractController
{
    #[Route(name: 'app_comunidad_index', methods: ['GET'])]
    public function index(ComunidadRepository $comunidadRepository): Response
    {
        return $this->render('comunidad/index.html.twig', [
            'comunidads' => $comunidadRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_comunidad_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $comunidad = new Comunidad();
        $form = $this->createForm(ComunidadType::class, $comunidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleThumbnailUpload($form, $comunidad, $slugger);
            $entityManager->persist($comunidad);
            $entityManager->flush();

            return $this->redirectToRoute('app_comunidad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comunidad/new.html.twig', [
            'comunidad' => $comunidad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comunidad_show', methods: ['GET'])]
    public function show(Comunidad $comunidad): Response
    {
        return $this->render('comunidad/show.html.twig', [
            'comunidad' => $comunidad,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comunidad_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comunidad $comunidad, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ComunidadType::class, $comunidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleThumbnailUpload($form, $comunidad, $slugger);
            $entityManager->flush();

            return $this->redirectToRoute('app_comunidad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comunidad/edit.html.twig', [
            'comunidad' => $comunidad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comunidad_delete', methods: ['POST'])]
    public function delete(Request $request, Comunidad $comunidad, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comunidad->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($comunidad);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_comunidad_index', [], Response::HTTP_SEE_OTHER);
    }

    private function handleThumbnailUpload(FormInterface $form, Comunidad $comunidad, SluggerInterface $slugger): void
    {
        $thumbnailFile = $form->get('thumbnail')->getData();

        if ($thumbnailFile === null) {
            return;
        }

        $uploadDirectory = $this->getParameter('kernel.project_dir').'/public/uploads/comunidad';

        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0775, true);
        }

        $originalFilename = pathinfo($thumbnailFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$thumbnailFile->guessExtension();

        try {
            $thumbnailFile->move($uploadDirectory, $newFilename);
        } catch (FileException $exception) {
            throw new \RuntimeException('No se pudo guardar la miniatura de la comunidad.', 0, $exception);
        }

        $comunidad->setThumbnail($newFilename);
    }
}
