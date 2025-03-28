<?php

namespace App\Entity;

use App\Repository\TareaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TareaRepository::class)]
class Tarea
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion = null;

    #[ORM\ManyToOne(targetEntity: Lista::class, inversedBy: "tareas")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lista $lista = null;

    #[ORM\Column]
    private ?bool $finalizada = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLista(): ?Lista 
{
    return $this->lista;
}

public function setLista(?Lista $lista): static 
{
    $this->lista = $lista;
    return $this;
}

public function isFinalizada(): ?bool
{
    return $this->finalizada;
}

public function setFinalizada(bool $finalizada): static
{
    $this->finalizada = $finalizada;

    return $this;
}
}
