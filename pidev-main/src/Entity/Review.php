<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $reviewer = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\Column]
    private ?int $note = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReviewer(): ?Utilisateur
    {
        return $this->reviewer;
    }

    public function setReviewer(?Utilisateur $reviewer): static
    {
        $this->reviewer = $reviewer;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }
}
