<?php

namespace App\Controller;

use App\Entity\Noticia;
use App\Form\NoticiaType;
use App\Repository\NoticiaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/noticia')]
final class NoticiaController extends AbstractController
{
    #[Route(name: 'app_noticia_index', methods: ['GET'])]
    public function index(NoticiaRepository $noticiaRepository): Response
    {
        return $this->render('noticia/index.html.twig', [
            'noticias' => $noticiaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_noticia_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[Autowire('%uploads_directory%')] string $uploadsDir): Response
    {
        $noticium = new Noticia();
        $form = $this->createForm(NoticiaType::class, $noticium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imagen = $form->get('imagen')->getData();
            if ($imagen) {
                 // Crear un nombre único para la imagen 
                $newFilename = uniqid() . '.' . $imagen->guessExtension();
     
                 // Mover el archivo al directorio de uploads
                $imagen->move($uploadsDir, $newFilename);
     
                 // Establecer la ruta de la imagen en noticia
                $noticium->setImagen($newFilename);
             }

            $entityManager->persist($noticium);
            $entityManager->flush();

            return $this->redirectToRoute('app_noticia_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('noticia/new.html.twig', [
            'noticium' => $noticium,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/publico', name: 'app_noticia_show_publico', methods: ['GET'])]
    public function showPublico(Noticia $noticium): Response
    {
        return $this->render('noticia/showPublico.html.twig', [
            'noticium' => $noticium,
        ]);
    }

    #[Route('/{id}', name: 'app_noticia_show', methods: ['GET'])]
    public function show(Noticia $noticium): Response
    {
        return $this->render('noticia/show.html.twig', [
            'noticium' => $noticium,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_noticia_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Noticia $noticium, EntityManagerInterface $entityManager, #[Autowire('%uploads_directory%')] string $uploadsDir): Response
    {
        $form = $this->createForm(NoticiaType::class, $noticium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
            $imagen = $form->get('imagen')->getData();
            if ($imagen) {
                 // Crear un nombre único para la imagen 
                $newFilename = uniqid() . '.' . $imagen->guessExtension();
     
                 // Mover el archivo al directorio de uploads
                $imagen->move($uploadsDir, $newFilename);
     
                 // Establecer la ruta de la imagen en noticia
                $noticium->setImagen($newFilename);
             }

            $entityManager->persist($noticium);
            $entityManager->flush();

            return $this->redirectToRoute('app_noticia_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('noticia/edit.html.twig', [
            'noticium' => $noticium,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_noticia_delete', methods: ['POST'])]
    public function delete(Request $request, Noticia $noticium, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$noticium->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($noticium);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_noticia_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
