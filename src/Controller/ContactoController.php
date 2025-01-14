<?php

namespace App\Controller;

use App\Form\ContactoType;
use App\Form\Model\Contacto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactoController extends AbstractController
{
    #[Route('/contacto', name: 'contacto')]
    public function contacto(Request $request, MailerInterface $mailer): Response
    {
        $contacto = new Contacto();
        $form = $this->createForm(ContactoType::class, $contacto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enviar email
            $email = (new Email())
                ->from($contacto->getEmail())
                ->to('tu-email@example.com')
                ->subject('Nuevo mensaje de contacto')
                ->text($contacto->getMensaje());

            $mailer->send($email);

            // Mensaje de éxito
            $this->addFlash('success', 'Mensaje enviado correctamente');

            return $this->redirectToRoute('contacto');
        }

        return $this->render('contacto/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
