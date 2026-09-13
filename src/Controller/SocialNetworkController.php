<?php

namespace App\Controller;

use App\Entity\SocialNetwork;
use App\Form\SocialNetworkType;
use App\Repository\SocialNetworkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/social/network')]
final class SocialNetworkController extends AbstractController
{
    #[Route(name: 'app_social_network_index', methods: ['GET'])]
    public function index(SocialNetworkRepository $socialNetworkRepository): Response
    {
        return $this->render('social_network/index.html.twig', [
            'social_networks' => $socialNetworkRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_social_network_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $socialNetwork = new SocialNetwork();
        $form = $this->createForm(SocialNetworkType::class, $socialNetwork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($socialNetwork);
            $entityManager->flush();

            return $this->redirectToRoute('app_social_network_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('social_network/new.html.twig', [
            'social_network' => $socialNetwork,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_social_network_show', methods: ['GET'])]
    public function show(SocialNetwork $socialNetwork): Response
    {
        return $this->render('social_network/show.html.twig', [
            'social_network' => $socialNetwork,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_social_network_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SocialNetwork $socialNetwork, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SocialNetworkType::class, $socialNetwork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_social_network_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('social_network/edit.html.twig', [
            'social_network' => $socialNetwork,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_social_network_delete', methods: ['POST'])]
    public function delete(Request $request, SocialNetwork $socialNetwork, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$socialNetwork->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($socialNetwork);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_social_network_index', [], Response::HTTP_SEE_OTHER);
    }
}
