<?php
namespace App\Controller;

use App\Entity\Actividades;
use App\Repository\ActividadesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ActividadesRepository $actividadesRepository): Response
    {
        return $this->render('home.html.twig', [
            'actividades' => $actividadesRepository->findAll(),
        ]);
    }

  
}