<?php

namespace App\Entity;

use App\Repository\NoticiaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoticiaRepository::class)]
class Noticia
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
    private ?string $descripcionDetallada = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagen = null;

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

    public function getDescripcionDetallada(): ?string
    {
        return $this->descripcionDetallada;
    }

    public function setDescripcionDetallada(string $descripcionDetallada): static
    {
        $this->descripcionDetallada = $descripcionDetallada;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): static
    {
        $this->imagen = $imagen;

        return $this;
    }
}
