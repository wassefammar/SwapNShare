<?php

namespace App\Entity;

use App\Repository\EchangeProduitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: EchangeProduitRepository::class)]
#[UniqueEntity(fields: ['produitIn', 'produitOut'], message: 'This exchange already exists')]
#[UniqueEntity(fields: ['produitIn', 'produitIn'], message: 'The  Poruct In is the Same as product Out ')]
class EchangeProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Please select a product')]
    private ?Produit $produitIn = null;

    #[ORM\OneToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Please select a product')]
    private ?Produit $produitOut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan(
        value: "today",
        message: "Please select a date in the future"
    )]
    private ?\DateTimeInterface $date_echange = null;

    #[ORM\Column(nullable: true)]
    private ?bool $valide = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduitIn(): ?Produit
    {
        return $this->produitIn;
    }

    public function setProduitIn(Produit $produitIn): static
    {
        $this->produitIn = $produitIn;

        return $this;
    }

    public function getProduitOut(): ?Produit
    {
        return $this->produitOut;
    }

    public function setProduitOut(Produit $produitOut): static
    {
        $this->produitOut = $produitOut;

        return $this;
    }

    public function getDateEchange(): ?\DateTimeInterface
    {
        return $this->date_echange;
    }

    public function setDateEchange(\DateTimeInterface $date_echange): static
    {
        $this->date_echange = $date_echange;

        return $this;
    }

    public function isValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(?bool $valide): static
    {
        $this->valide = $valide;
        return $this;
    }
}
