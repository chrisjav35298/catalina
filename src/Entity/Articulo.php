<?php

namespace App\Entity;

use App\Repository\ArticuloRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticuloRepository::class)]
class Articulo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contenido = null;

    #[ORM\ManyToOne(inversedBy: 'articulos')]
    private ?Ley $ley = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getContenido(): ?string
    {
        return $this->contenido;
    }

    public function setContenido(?string $contenido): static
    {
        $this->contenido = $contenido;

        return $this;
    }

    public function getLey(): ?Ley
    {
        return $this->ley;
    }

    public function setLey(?Ley $ley): static
    {
        $this->ley = $ley;

        return $this;
    }
}
