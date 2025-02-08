<?php

namespace App\Controller;

use App\Entity\Equipo;
use App\Form\EquipoType;
use App\Repository\EquipoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/equipo')]
#[IsGranted('ROLE_ADMIN')]
final class EquipoController extends AbstractController
{
    #[Route(name: 'app_equipo_index', methods: ['GET'])]
    public function index(EquipoRepository $equipoRepository): Response
    {
        return $this->render('equipo/index.html.twig', [
            'equipos' => $equipoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_equipo_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager, #[Autowire('%uploads_directory%')] string $uploadsDir): Response
    {
        $equipo = new Equipo();
        $form = $this->createForm(EquipoType::class, $equipo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //se van a tratar la subida de imagen
            $imagen = $form->get('imagen')->getData();
            if ($imagen) {
                 // Crear un nombre único para la imagen 
                $newFilename = uniqid() . '.' . $imagen->guessExtension();
                 // Mover el archivo al directorio de uploads
                $imagen->move($uploadsDir, $newFilename);
                 // Establecer la ruta de la imagen en noticia
                $equipo->setImagen($newFilename);
            }
            $entityManager->persist($equipo);
            $entityManager->flush();
            return $this->redirectToRoute('app_equipo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipo/new.html.twig', [
            'equipo' => $equipo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipo_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(Equipo $equipo): Response
    {
        return $this->render('equipo/show.html.twig', [
            'equipo' => $equipo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipo_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Equipo $equipo, EntityManagerInterface $entityManager,#[Autowire('%uploads_directory%')] string $uploadsDir): Response
    {
        $form = $this->createForm(EquipoType::class, $equipo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //se van a tratar la subida de imagen
            $imagen = $form->get('imagen')->getData();
            if ($imagen) {
                 // Crear un nombre único para la imagen 
                $newFilename = uniqid() . '.' . $imagen->guessExtension();
                 // Mover el archivo al directorio de uploads
                $imagen->move($uploadsDir, $newFilename);
                 // Establecer la ruta de la imagen en noticia
                $equipo->setImagen($newFilename);
            }
            $entityManager->persist($equipo);
            $entityManager->flush();

            return $this->redirectToRoute('app_equipo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipo/edit.html.twig', [
            'equipo' => $equipo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipo_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Equipo $equipo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipo->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($equipo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipo_index', [], Response::HTTP_SEE_OTHER);
    }
}
