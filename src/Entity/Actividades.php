<?php

namespace App\Entity;

use App\Repository\ActividadesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActividadesRepository::class)]
class Actividades
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcionCorta = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcionLarga = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagenDestacada = null;

    #[ORM\Column(length: 255)]
    private ?string $imagenes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDescripcionCorta(): ?string
    {
        return $this->descripcionCorta;
    }

    public function setDescripcionCorta(string $descripcionCorta): static
    {
        $this->descripcionCorta = $descripcionCorta;

        return $this;
    }

    public function getDescripcionLarga(): ?string
    {
        return $this->descripcionLarga;
    }

    public function setDescripcionLarga(string $descripcionLarga): static
    {
        $this->descripcionLarga = $descripcionLarga;

        return $this;
    }

    public function getImagenDestacada(): ?string
    {
        return $this->imagenDestacada;
    }

    public function setImagenDestacada(?string $imagenDestacada): static
    {
        $this->imagenDestacada = $imagenDestacada;

        return $this;
    }

    public function getImagenes(): ?string
    {
        return $this->imagenes;
    }

    public function setImagenes(string $imagenes): static
    {
        $this->imagenes = $imagenes;

        return $this;
    }
}
