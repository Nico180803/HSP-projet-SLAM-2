<?php

namespace App\Entity;

use App\Repository\UserEvenementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserEvenementRepository::class)]
class UserEvenement
{
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
}
