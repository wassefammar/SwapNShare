<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"The Title is mandatory")]
    private ?string $titreService = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"The description is mandatory")]
    private ?string $descriptionService = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"please add an address")]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]   
    #[Assert\NotBlank(message:"please add an image")]
    private ?string $photo = null;

    #[ORM\Column]
    private ?bool $choixEchange = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message:"The Category is mandatory")]
    private ?Categorie $categorie = null;


    #[ORM\OneToMany(mappedBy: 'service', targetEntity: Commentaire::class, orphanRemoval: true)]
    private Collection $commentaires;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message:"The user is mandatory")]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column]
    private ?bool $valid = null;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreService(): ?string
    {
        return $this->titreService;
    }

    public function setTitreService(string $titreService): static
    {
        $this->titreService = $titreService;

        return $this;
    }

    public function getDescriptionService(): ?string
    {
        return $this->descriptionService;
    }

    public function setDescriptionService(string $descriptionService): static
    {
        $this->descriptionService = $descriptionService;

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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

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

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setService($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getService() === $this) {
                $commentaire->setService(null);
            }
        }

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

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): static
    {
        $this->valid = $valid;

        return $this;
    }
}
