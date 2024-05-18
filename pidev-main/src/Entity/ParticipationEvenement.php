<?php

namespace App\Entity;

use App\Repository\ParticipationEvenementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipationEvenementRepository::class)]
class ParticipationEvenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'participationEvenement', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Evenement $evenement = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $Utilisateur = null;

    #[ORM\Column(nullable: true)]
    private ?float $offre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(Evenement $evenement): static
    {
        $this->evenement = $evenement;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->Utilisateur;
    }

    public function setUtilisateur(?Utilisateur $Utilisateur): static
    {
        $this->Utilisateur = $Utilisateur;

        return $this;
    }

    public function getOffre(): ?float
    {
        return $this->offre;
    }

    public function setOffre(?float $offre): static
    {
        $this->offre = $offre;

        return $this;
    }
}
