<?php

namespace App\Entity;

use App\Repository\LeyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeyRepository::class)]
class Ley
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fechaSancion = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fechaPromulgacion = null;

    #[ORM\Column]
    private ?bool $activo = null;

    /**
     * @var Collection<int, Articulo>
     */
    #[ORM\OneToMany(targetEntity: Articulo::class, mappedBy: 'ley', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $articulos;

    public function __construct()
    {
        $this->articulos = new ArrayCollection();
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getFechaSancion(): ?\DateTimeInterface
    {
        return $this->fechaSancion;
    }

    public function setFechaSancion(\DateTimeInterface $fechaSancion): static
    {
        $this->fechaSancion = $fechaSancion;

        return $this;
    }

    public function getFechaPromulgacion(): ?\DateTimeInterface
    {
        return $this->fechaPromulgacion;
    }

    public function setFechaPromulgacion(?\DateTimeInterface $fechaPromulgacion): static
    {
        $this->fechaPromulgacion = $fechaPromulgacion;

        return $this;
    }

    public function isActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): static
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * @return Collection<int, Articulo>
     */
    public function getArticulos(): Collection
    {
        return $this->articulos;
    }

    public function addArticulo(Articulo $articulo): static
    {
        if (!$this->articulos->contains($articulo)) {
            $this->articulos->add($articulo);
            $articulo->setLey($this);
        }

        return $this;
    }

    public function removeArticulo(Articulo $articulo): static
    {
        if ($this->articulos->removeElement($articulo)) {
            // set the owning side to null (unless already changed)
            if ($articulo->getLey() === $this) {
                $articulo->setLey(null);
            }
        }

        return $this;
    }
}
