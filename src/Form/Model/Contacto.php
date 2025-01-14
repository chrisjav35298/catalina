<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Contacto
{
    #[Assert\NotBlank(message: "El nombre es obligatorio")]
    private ?string $nombre = null;

    #[Assert\NotBlank(message: "El email es obligatorio")]
    #[Assert\Email(message: "Por favor, ingresa un email válido")]
    private ?string $email = null;

    private ?string $telefono = null;

    #[Assert\NotBlank(message: "El mensaje no puede estar vacío")]
    #[Assert\Length(min: 10, minMessage: "El mensaje debe tener al menos {{ limit }} caracteres")]
    private ?string $mensaje = null;

    // Getters y setters
    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): void
    {
        $this->telefono = $telefono;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(?string $mensaje): void
    {
        $this->mensaje = $mensaje;
    }
}
