<?php

namespace App\Entity;

use App\Repository\CommentairesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentairesRepository::class)]
class Commentaires
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reponse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pj = null;

    #[ORM\Column]
    private ?\DateTime $date_creation = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $refUser = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'reponses')]
    private ?self $refCommentaire = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'refCommentaire')]
    private Collection $reponses;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sujets $refSujet = null;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): static
    {
        $this->reponse = $reponse;

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

    public function getRefUser(): ?User
    {
        return $this->refUser;
    }

    public function setRefUser(?User $refUser): static
    {
        $this->refUser = $refUser;

        return $this;
    }

    public function getRefCommentaire(): ?self
    {
        return $this->refCommentaire;
    }

    public function setRefCommentaire(?self $refCommentaire): static
    {
        $this->refCommentaire = $refCommentaire;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(self $reponse): static
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setRefCommentaire($this);
        }

        return $this;
    }

    public function removeReponse(self $reponse): static
    {
        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getRefCommentaire() === $this) {
                $reponse->setRefCommentaire(null);
            }
        }

        return $this;
    }

    public function getRefSujet(): ?Sujets
    {
        return $this->refSujet;
    }

    public function setRefSujet(?Sujets $refSujet): static
    {
        $this->refSujet = $refSujet;

        return $this;
    }
}
