<?php

namespace App\Controller;

use App\Entity\CarruselRefugio;
use App\Form\CarruselRefugioType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CarruselRefugioRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/carrusel/refugio')]
final class CarruselRefugioController extends AbstractController
{
    #[Route(name: 'app_carrusel_refugio_index', methods: ['GET'])]
    public function index(CarruselRefugioRepository $carruselRefugioRepository): Response
    {
        return $this->render('carrusel_refugio/index.html.twig', [
            'carrusel_refugios' => $carruselRefugioRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_carrusel_refugio_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[Autowire('%uploads_directory%')] string $uploadsDir): Response
    {
        $carruselRefugio = new CarruselRefugio();
        $form = $this->createForm(CarruselRefugioType::class, $carruselRefugio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imagen = $form->get('imagen')->getData();
            if ($imagen) {
                 // Crear un nombre único para la imagen 
                $newFilename = uniqid() . '.' . $imagen->guessExtension();
     
                 // Mover el archivo al directorio de uploads
                $imagen->move($uploadsDir, $newFilename);
     
                 // Establecer la ruta de la imagen en noticia
                $carruselRefugio->setImage($newFilename);
             }

            $entityManager->persist($carruselRefugio);
            $entityManager->flush();

            return $this->redirectToRoute('app_carrusel_refugio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('carrusel_refugio/new.html.twig', [
            'carrusel_refugio' => $carruselRefugio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carrusel_refugio_show', methods: ['GET'])]
    public function show(CarruselRefugio $carruselRefugio): Response
    {
        return $this->render('carrusel_refugio/show.html.twig', [
            'carrusel_refugio' => $carruselRefugio,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_carrusel_refugio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CarruselRefugio $carruselRefugio, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarruselRefugioType::class, $carruselRefugio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_carrusel_refugio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('carrusel_refugio/edit.html.twig', [
            'carrusel_refugio' => $carruselRefugio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carrusel_refugio_delete', methods: ['POST'])]
    public function delete(Request $request, CarruselRefugio $carruselRefugio, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$carruselRefugio->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($carruselRefugio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_carrusel_refugio_index', [], Response::HTTP_SEE_OTHER);
    }
}
