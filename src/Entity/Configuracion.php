<?php

namespace App\Entity;

use App\Repository\ConfiguracionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfiguracionRepository::class)]
class Configuracion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $telefonoContacto = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $whatsappNumero = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $whatsappMensaje = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $emailContacto = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $aliasDonaciones = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $bancoNombre = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $cbu = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $cuit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTelefonoContacto(): ?string
    {
        return $this->telefonoContacto;
    }

    public function setTelefonoContacto(?string $telefonoContacto): static
    {
        $this->telefonoContacto = $telefonoContacto;

        return $this;
    }

    public function getWhatsappNumero(): ?string
    {
        return $this->whatsappNumero;
    }

    public function setWhatsappNumero(?string $whatsappNumero): static
    {
        $this->whatsappNumero = $whatsappNumero;

        return $this;
    }

    public function getWhatsappMensaje(): ?string
    {
        return $this->whatsappMensaje;
    }

    public function setWhatsappMensaje(?string $whatsappMensaje): static
    {
        $this->whatsappMensaje = $whatsappMensaje;

        return $this;
    }

    public function getEmailContacto(): ?string
    {
        return $this->emailContacto;
    }

    public function setEmailContacto(?string $emailContacto): static
    {
        $this->emailContacto = $emailContacto;

        return $this;
    }

    public function getAliasDonaciones(): ?string
    {
        return $this->aliasDonaciones;
    }

    public function setAliasDonaciones(?string $aliasDonaciones): static
    {
        $this->aliasDonaciones = $aliasDonaciones;

        return $this;
    }
        public function getBancoNombre(): ?string
    {
        return $this->bancoNombre;
    }

    public function setBancoNombre(?string $bancoNombre): static
    {
        $this->bancoNombre = $bancoNombre;
        return $this;
    }

    public function getCbu(): ?string
    {
        return $this->cbu;
    }

    public function setCbu(?string $cbu): static
    {
        $this->cbu = $cbu;
        return $this;
    }

    public function getCuit(): ?string
    {
        return $this->cuit;
    }

    public function setCuit(?string $cuit): static
    {
        $this->cuit = $cuit;
        return $this;
    }
}
