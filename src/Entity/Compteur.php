<?php

namespace App\Entity;

use App\Repository\CompteurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteurRepository::class)]
class Compteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $films_qte = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilmsQte(): ?int
    {
        return $this->films_qte;
    }

    public function setFilmsQte(int $films_qte): static
    {
        $this->films_qte = $films_qte;

        return $this;
    }
}
