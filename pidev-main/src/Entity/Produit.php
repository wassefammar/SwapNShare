<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"this field cannot be blanked")]
    private ?string $titreProduit = null;
   

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message:"this field cannot be blanked")]
    private ?string $descriptionProduit = null;

    #[ORM\Column(length: 255, nullable:true)]
    #[Assert\NotBlank(message:"this field cannot be blanked")]
    private ?string $photo = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"this field cannot be blanked")]
    private ?string $ville = null;

    #[ORM\Column]
    private ?bool $choixEchange = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"this field cannot be blanked")]
    private ?string $etat = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"this field cannot be blanked")]
    private ?float $prix = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[Assert\NotBlank(message:"this field cannot be blanked")]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[Assert\NotBlank(message:"this field cannot be blanked")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Review::class, orphanRemoval: true)]
    private Collection $reviews;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }
   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreProduit(): ?string
    {
        return $this->titreProduit;
    }

    public function setTitreProduit(string $titreProduit): static
    {
        $this->titreProduit = $titreProduit;

        return $this;
    }

    public function getDescriptionProduit(): ?string
    {
        return $this->descriptionProduit;
    }

    public function setDescriptionProduit(string $descriptionProduit): static
    {
        $this->descriptionProduit = $descriptionProduit;

        return $this;
    }

    public function getPhoto() : ?string
    {
        return $this->photo;
    }

    public function setPhoto (?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function isChoixEchange(): ?bool
    {
        return $this->choixEchange;
    }

    public function setChoixEchange(bool $choixEchange): static
    {
        $this->choixEchange = $choixEchange;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setProduit($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getProduit() === $this) {
                $review->setProduit(null);
            }
        }

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

 
}
