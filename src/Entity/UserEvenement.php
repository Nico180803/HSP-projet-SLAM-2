<?php

namespace App\Entity;

use App\Repository\UserEvenementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserEvenementRepository::class)]
class UserEvenement
{
    #[ORM\ManyToOne(targetEntity: Evenements::class, inversedBy: 'userEvenements')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userEvenements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $refUser = null;

    #[ORM\ManyToOne(inversedBy: 'userEvenements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Evenements $refEvenement = null;

    #[ORM\Column]
    private ?bool $isResponsable = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $statut = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateInscription = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefUser(): ?User
    {
        return $this->refUser;
    }

    public function setRefUser(?User $refUser): static
    {
        $this->refUser = $refUser;
        return $this;
    }

    public function getRefEvenement(): ?Evenements
    {
        return $this->refEvenement;
    }

    public function setRefEvenement(?Evenements $refEvenement): static
    {
        $this->refEvenement = $refEvenement;
        return $this;
    }

    public function isResponsable(): ?bool
    {
        return $this->isResponsable;
    }

    public function setIsResponsable(bool $isResponsable): static
    {
        $this->isResponsable = $isResponsable;
        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(?\DateTimeInterface $dateInscription): static
    {
        $this->dateInscription = $dateInscription;
        return $this;
    }
}

