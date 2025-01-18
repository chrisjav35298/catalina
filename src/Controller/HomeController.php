<?php
namespace App\Controller;

use App\Form\Model\Contacto;
use App\Form\ContactoType;
use App\Repository\NoticiaRepository;
use App\Repository\ActividadesRepository;
use App\Repository\EquipoRepository;
use App\Repository\LeyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        ActividadesRepository $actividadesRepository,
        NoticiaRepository $noticiaRepository,
        MailerInterface $mailer,
        LeyRepository $leyRepository,
        EquipoRepository $equipoRepository
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
       $leyes = $leyRepository->findAll(); 
        if (empty($leyes)) {
            $leyes = []; // Si no hay actividades, asegúrate de pasar un array vacío.
        }
        $equipo = $equipoRepository->findAll(); 
        if (empty($equipo)) {
            $equipo = []; // Si no hay actividades, asegúrate de pasar un array vacío.
        }

        // Actividades y noticias limitadas para paginar
        
        $actividades = $actividadesRepository->findBy(
            [], // Sin criterios adicionales de búsqueda
            ['id' => 'DESC'], // Ordenar por ID en orden descendente (últimas primero)
            6 // Limitar a las 6 actividades más recientes
        );

        if (empty($actividades)) {
            $actividades = []; // Si no hay actividades, asegúrate de pasar un array vacío.
        }

        $noticias = $noticiaRepository->findBy(
            [], // Sin criterios adicionales de búsqueda
            ['id' => 'DESC'], // Ordenar por ID en orden descendente (últimas primero)
            3 // Limitar a las 6 actividades más recientes
        ); 
        if (empty($noticias)) {
            $noticias = []; // Si no hay actividades, asegúrate de pasar un array vacío.
        }
        // Renderizar el template con los datos obtenidos
        return $this->render('home.html.twig', [
            'contactForm' => $form->createView(), // Pasar la vista del formulario
            'actividades' => $actividades, // Pasar actividades
            'noticias' => $noticias, // Pasar noticias
            'user' => $user, 
            'leyes' => $leyes,
            'equipos' => $equipo, // Pasar noticias

        ]);
    }

    #[Route('actpublico/', name: 'actividades_index_publico', methods: ['GET'])]
    public function indexActividades(
        ActividadesRepository $actividadesRepository, 
        PaginatorInterface $paginator, 
        Request $request
    ): Response {
        // Obtén una consulta para las actividades
        $queryActividades = $actividadesRepository->createQueryBuilder('a') // Usa QueryBuilder para obtener la consulta
            ->getQuery();

        // Pagina los resultados
        $actividades = $paginator->paginate(
            $queryActividades,                     // Consulta de actividades
            $request->query->getInt('page', 1),    // Página actual, por defecto 1
            6                                    // Cantidad de resultados por página
        );

        // Renderiza la plantilla con los datos paginados
        return $this->render('actividades/indexPublico.html.twig', [
            'actividades' => $actividades,
        ]);
    }

    #[Route('/notpublico',name: 'noticias_index_publico', methods: ['GET'])]
    public function indexNoticias(NoticiaRepository $noticiaRepository, 
    PaginatorInterface $paginator, 
    Request $request
): Response {
    // Obtén una consulta para las actividades
    $queryNoticias = $noticiaRepository->createQueryBuilder('a') // Usa QueryBuilder para obtener la consulta
        ->getQuery();

    // Pagina los resultados
    $noticias = $paginator->paginate(
        $queryNoticias,                     // Consulta de actividades
        $request->query->getInt('page', 1),    // Página actual, por defecto 1
        6                                    // Cantidad de resultados por página
    );

        return $this->render('noticia/indexPublico.html.twig', [
            'noticias' => $noticias,
        ]);
    }

}
