<?php

namespace App\Entity;

use App\Repository\ActividadesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

 
    /**
     * @var Collection<int, Imagen>
     */
    // #[ORM\OneToMany(targetEntity: Imagen::class, mappedBy: 'Actividad')]
    #[ORM\OneToMany(mappedBy: 'Actividad', targetEntity: Imagen::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $imagenes;

    public function __construct()
    {
        $this->imagenes = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Imagen>
     */
    public function getImagenes(): Collection
    {
        return $this->imagenes;
    }

    public function addImagen(Imagen $imagen): static
    {
        if (!$this->imagenes->contains($imagen)) {
            $this->imagenes->add($imagen);
            $imagen->setActividad($this);
        }

        return $this;
    }

    public function removeImagen(Imagen $imagen): static
    {
        if ($this->imagenes->removeElement($imagen)) {
            // set the owning side to null (unless already changed)
            if ($imagen->getActividad() === $this) {
                $imagen->setActividad(null);
            }
        }

        return $this;
    }
}
