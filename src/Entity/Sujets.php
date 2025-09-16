<?php

namespace App\Entity;

use App\Repository\SujetsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SujetsRepository::class)]
class Sujets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pj = null;

    #[ORM\Column]
    private ?\DateTime $date_creation = null;

    /**
     * @var Collection<int, Commentaires>
     */
    #[ORM\OneToMany(targetEntity: Commentaires::class, mappedBy: 'refSujet', orphanRemoval: true)]
    private Collection $commentaires;

    #[ORM\ManyToOne(inversedBy: 'sujets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Flux $refFlux = null;

    #[ORM\ManyToOne(inversedBy: 'sujets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $refUser = null;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getPj(): ?string
    {
        return $this->pj;
    }

    public function setPj(?string $pj): static
    {
        $this->pj = $pj;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTime $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    /**
     * @return Collection<int, Commentaires>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaires $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setRefSujet($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaires $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getRefSujet() === $this) {
                $commentaire->setRefSujet(null);
            }
        }

        return $this;
    }

    public function getRefFlux(): ?Flux
    {
        return $this->refFlux;
    }

    public function setRefFlux(?Flux $refFlux): static
    {
        $this->refFlux = $refFlux;

        return $this;
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
}
