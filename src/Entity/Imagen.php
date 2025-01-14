<?php

namespace App\Entity;

use App\Repository\ImagenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagenRepository::class)]
class Imagen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ruta = null;

    #[ORM\ManyToOne(inversedBy: 'imagenes', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Actividades $Actividad = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRuta(): ?string
    {
        return $this->ruta;
    }

    public function setRuta(string $ruta): static
    {
        $this->ruta = $ruta;

        return $this;
    }

    public function getActividad(): ?Actividades
    {
        return $this->Actividad;
    }

    public function setActividad(?Actividades $Actividad): static
    {
        $this->Actividad = $Actividad;

        return $this;
    }
}
