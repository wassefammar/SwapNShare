<?php

namespace App\Entity;

use App\Repository\WishListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WishListRepository::class)]
class WishList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'wishList', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;


    #[ORM\OneToMany(mappedBy: 'wishList', targetEntity: ProduitFavoris::class, orphanRemoval: true)]
    private Collection $produitFavoris;

    public function __construct()
    {
        $this->produitFavoris = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }



    /**
     * @return Collection<int, ProduitFavoris>
     */
    public function getProduitFavoris(): Collection
    {
        return $this->produitFavoris;
    }

    public function addProduitFavori(ProduitFavoris $produitFavori): static
    {
        if (!$this->produitFavoris->contains($produitFavori)) {
            $this->produitFavoris->add($produitFavori);
            $produitFavori->setWishList($this);
        }

        return $this;
    }

    public function removeProduitFavori(ProduitFavoris $produitFavori): static
    {
        if ($this->produitFavoris->removeElement($produitFavori)) {
            // set the owning side to null (unless already changed)
            if ($produitFavori->getWishList() === $this) {
                $produitFavori->setWishList(null);
            }
        }

        return $this;
    }
}
