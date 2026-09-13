<?php

namespace App\Controller;

use App\Form\ConfiguracionType;
use App\Repository\ConfiguracionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/configuracion')]
class ConfiguracionController extends AbstractController
{
    #[Route('/', name: 'app_configuracion_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ConfiguracionRepository $configuracionRepository,
        EntityManagerInterface $em
    ): Response {
        // Trae el único registro existente o crea uno en blanco si no hay
        $configuracion = $configuracionRepository->getAjustes();

        $form = $this->createForm(ConfiguracionType::class, $configuracion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Los ajustes generales se guardaron correctamente.');

            return $this->redirectToRoute('app_configuracion_edit');
        }

        return $this->render('configuracion/editar.html.twig', [
            'form' => $form->createView(),
            'configuracion' => $configuracion,
        ]);
    }
}