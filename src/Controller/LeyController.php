<?php

namespace App\Controller;

use App\Entity\Ley;
use App\Form\LeyType;
use App\Repository\LeyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/ley')]
#[IsGranted('ROLE_ADMIN')]
final class LeyController extends AbstractController
{
    #[Route(name: 'app_ley_index', methods: ['GET'])]
    public function index(LeyRepository $leyRepository): Response
    {
        return $this->render('ley/index.html.twig', [
            'leys' => $leyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ley_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ley = new Ley();
        $form = $this->createForm(LeyType::class, $ley);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ley);
            $entityManager->flush();

            return $this->redirectToRoute('app_ley_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ley/new.html.twig', [
            'ley' => $ley,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ley_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(Ley $ley): Response
    {
        return $this->render('ley/show.html.twig', [
            'ley' => $ley,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ley_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Ley $ley, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LeyType::class, $ley);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ley_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ley/edit.html.twig', [
            'ley' => $ley,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ley_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Ley $ley, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ley->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ley);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ley_index', [], Response::HTTP_SEE_OTHER);
    }
}
