<?php

namespace App\Entity;

use App\Repository\EchangeServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EchangeServiceRepository::class)]
class EchangeService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Please select a service')]
    private ?Service $serviceIn = null;

    #[ORM\OneToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Please select a service')]
    private ?Service $serviceOut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan(
        value: "today",
        message: "Please select a date in the future"
    )]
    private ?\DateTimeInterface $date_echange = null;

    #[ORM\Column(nullable: true)]
    #[Assert\IsTrue(message: 'Valid Box must be Checked.')]
    private ?bool $valide = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getServiceIn(): ?Service
    {
        return $this->serviceIn;
    }
    public function setServiceIn(Service $serviceIn): static
    {
        $this->serviceIn = $serviceIn;
        return $this;
    }
    public function getServiceOut(): ?Service
    {
        return $this->serviceOut;
    }
    public function setServiceOut(Service $serviceOut): static
    {
        $this->serviceOut = $serviceOut;
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
