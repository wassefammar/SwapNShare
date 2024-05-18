<?php

namespace App\Entity;

use App\Repository\ProduitFavorisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitFavorisRepository::class)]
class ProduitFavoris
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'produitFavoris')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WishList $wishList = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWishList(): ?WishList
    {
        return $this->wishList;
    }

    public function setWishList(?WishList $wishList): static
    {
        $this->wishList = $wishList;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(Produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }
}
