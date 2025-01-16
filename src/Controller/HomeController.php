<?php
namespace App\Controller;

use App\Form\Model\Contacto;
use App\Form\ContactoType;
use App\Repository\NoticiaRepository;
use App\Repository\ActividadesRepository;
use App\Repository\LeyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        ActividadesRepository $actividadesRepository,
        NoticiaRepository $noticiaRepository,
        MailerInterface $mailer,
        LeyRepository $leyRepository,
    ): Response {
        // Obtener usuario si está logueado
        $user = $this->getUser();

        // Crear objeto de datos y formulario
        $contacto = new Contacto();
        $form = $this->createForm(ContactoType::class, $contacto);

        $form->handleRequest($request);

        // Manejar el envío del formulario
        if ($form->isSubmitted() && $form->isValid()) {
            // Crear email
            $email = (new Email())
                ->from($contacto->getEmail()) // Correo del remitente
                ->to('tu-email@example.com') // Cambiar por tu correo
                ->subject('Nuevo mensaje de contacto') // Asunto del mensaje
                ->text($contacto->getMensaje()); // Contenido

            // Enviar el email
            $mailer->send($email);

            // Mostrar mensaje de éxito
            $this->addFlash('success', 'Mensaje enviado, estaremos en contacto con vos en breve!');

            // Redirigir para evitar reenvío de formulario
            return $this->redirectToRoute('app_home');
        }

        // Consultas antes de renderizar el template
        $actividades = $actividadesRepository->findAll(); 
        if (empty($actividades)) {
            $actividades = []; // Si no hay actividades, asegúrate de pasar un array vacío.
        }
        $noticias = $noticiaRepository->findAll(); 
        if (empty($noticias)) {
            $noticias = []; // Si no hay actividades, asegúrate de pasar un array vacío.
        }
        $leyes = $leyRepository->findAll(); 
        if (empty($leyes)) {
            $leyes = []; // Si no hay actividades, asegúrate de pasar un array vacío.
        }
        // Renderizar el template con los datos obtenidos
        return $this->render('home.html.twig', [
            'contactForm' => $form->createView(), // Pasar la vista del formulario
            'actividades' => $actividades, // Pasar actividades
            'noticias' => $noticias, // Pasar noticias
            'user' => $user, 
            'leyes' => $leyes,// Pasar el usuario a la vista
        ]);
    }
}
