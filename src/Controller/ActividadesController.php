<?php

namespace App\Controller;

use App\Entity\Imagen;
use App\Entity\Actividades;
use App\Form\ActividadesType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ActividadesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/actividades')]
final class ActividadesController extends AbstractController
{
    #[Route(name: 'app_actividades_index', methods: ['GET'])]
    public function index(ActividadesRepository $actividadesRepository): Response
    {
        return $this->render('actividades/index.html.twig', [
            'actividades' => $actividadesRepository->findAll(),
        ]);
    }



    #[Route('/new', name: 'app_actividades_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[Autowire('%uploads_directory%')] string $uploadsDir): Response
    {
        $actividade = new Actividades();
        $form = $this->createForm(ActividadesType::class, $actividade);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            
            // 1. Manejo de imagen destacada
            $imagenDestacada = $form->get('imagenDestacada')->getData();
            if ($imagenDestacada) {
                // Crear un nombre único para la imagen destacada
                $newFilenameDestacada = uniqid() . '.' . $imagenDestacada->guessExtension();
    
                // Mover el archivo al directorio de uploads
                $imagenDestacada->move($uploadsDir, $newFilenameDestacada);
    
                // Establecer la ruta de la imagen destacada en la actividad
                $actividade->setImagenDestacada($newFilenameDestacada);
            }
            
            // 2. Manejo de las imágenes adicionales
            foreach ($form->get('imagenes') as $imagenForm) {
                /** @var UploadedFile|null $imagenArchivo */
                $imagenArchivo = $imagenForm->get('ruta')->getData();
            
                // SOLO procesar si hay una imagen válida
                if ($imagenArchivo instanceof UploadedFile) {
                    $newFilename = uniqid() . '.' . $imagenArchivo->guessExtension();
                    $imagenArchivo->move($uploadsDir, $newFilename);
            
                    $imagen = new Imagen();
                    $imagen->setRuta($newFilename);
                    $actividade->addImagen($imagen);
            
                    $entityManager->persist($imagen);
                }
            }
    

            $entityManager->persist($actividade);
            $entityManager->flush(); // Ejecutar todas las operaciones en la base de datos
    
            return $this->redirectToRoute('app_actividades_index');
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
    public function edit(Request $request, Actividades $actividade, EntityManagerInterface $entityManager, #[Autowire('%uploads_directory%')] string $uploadsDir): Response
    {
        $form = $this->createForm(ActividadesType::class, $actividade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 1. Manejo de la imagen destacada
            $imagenDestacada = $form->get('imagenDestacada')->getData();
            if ($imagenDestacada) {
                // Eliminar la imagen anterior si existe
                if ($actividade->getImagenDestacada() && file_exists($uploadsDir . '/' . $actividade->getImagenDestacada())) {
                    unlink($uploadsDir . '/' . $actividade->getImagenDestacada());
                }

                // Crear un nombre único para la nueva imagen destacada
                $newFilenameDestacada = uniqid() . '.' . $imagenDestacada->guessExtension();

                // Mover el archivo al directorio de uploads
                $imagenDestacada->move($uploadsDir, $newFilenameDestacada);

                // Establecer la nueva ruta de la imagen destacada
                $actividade->setImagenDestacada($newFilenameDestacada);
            }

            // 2. Manejo de nuevas imágenes adicionales
            $imagenesForm = $form->get('imagenes');
            foreach ($imagenesForm as $imagenForm) {
                /** @var UploadedFile|null $imagenArchivo */
                $imagenArchivo = $imagenForm->get('ruta')->getData();

                if ($imagenArchivo) {
                    // Crear un nombre único para la nueva imagen
                    $newFilename = uniqid() . '.' . $imagenArchivo->guessExtension();

                    // Mover el archivo al directorio de uploads
                    $imagenArchivo->move($uploadsDir, $newFilename);

                    // Crear una nueva entidad de imagen
                    $imagen = new Imagen();
                    $imagen->setRuta($newFilename);
                    $imagen->setActividad($actividade); // Asociar la imagen a la actividad

                    // Agregar la nueva imagen a la actividad
                    $actividade->addImagen($imagen);

                    // Persistir la nueva imagen en la base de datos
                    $entityManager->persist($imagen);
                }
            }

            // Guardar los cambios realizados en la actividad
            $entityManager->flush();

            return $this->redirectToRoute('app_actividades_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('actividades/edit.html.twig', [
            'actividade' => $actividade,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_actividades_delete', methods: ['POST'])]
    public function delete(Request $request, Actividades $actividade, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actividade->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($actividade);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_actividades_index', [], Response::HTTP_SEE_OTHER);
    }
}
