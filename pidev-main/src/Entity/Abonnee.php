<?php

namespace App\Entity;

use App\Repository\AbonneeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbonneeRepository::class)]
class Abonnee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $troqueur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $abonnee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTroqueur(): ?Utilisateur
    {
        return $this->troqueur;
    }

    public function setTroqueur(?Utilisateur $troqueur): static
    {
        $this->troqueur = $troqueur;

        return $this;
    }

    public function getAbonnee(): ?Utilisateur
    {
        return $this->abonnee;
    }

    public function setAbonnee(?Utilisateur $abonnee): static
    {
        $this->abonnee = $abonnee;

        return $this;
    }
}
