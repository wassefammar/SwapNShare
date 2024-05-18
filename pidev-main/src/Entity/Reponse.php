<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Please select the designed reason")]
    private ?string $titreR = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Please provide further details")]
    private ?string $descriptionR = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?reclamation $reponse = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreR(): ?string
    {
        return $this->titreR;
    }

    public function setTitreR(string $titreR): static
    {
        $this->titreR = $titreR;

        return $this;
    }

    public function getDescriptionR(): ?string
    {
        return $this->descriptionR;
    }

    public function setDescriptionR(string $descriptionR): static
    {
        $this->descriptionR = $descriptionR;

        return $this;
    }

    public function getReponse(): ?reclamation
    {
        return $this->reponse;
    }

    public function setReponse(reclamation $reponse): static
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
