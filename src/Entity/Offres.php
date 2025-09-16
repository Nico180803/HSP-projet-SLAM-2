<?php

namespace App\Entity;

use App\Repository\OffresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffresRepository::class)]
class Offres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?float $salaire = null;

    #[ORM\Column]
    private ?\DateTime $date_creation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $date_fermeture = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pj = null;

    /**
     * @var Collection<int, ContactsEntreprise>
     */
    #[ORM\ManyToMany(targetEntity: ContactsEntreprise::class, mappedBy: 'refOffre')]
    private Collection $contactsEntreprises;

    #[ORM\ManyToOne(inversedBy: 'offres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypesOffres $refTypesOffre = null;

    #[ORM\ManyToOne(inversedBy: 'offres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $refCreateur = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'candidatures')]
    private Collection $refUser;

    public function __construct()
    {
        $this->contactsEntreprises = new ArrayCollection();
        $this->refUser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function setSalaire(?float $salaire): static
    {
        $this->salaire = $salaire;

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

    public function getDateFermeture(): ?\DateTime
    {
        return $this->date_fermeture;
    }

    public function setDateFermeture(?\DateTime $date_fermeture): static
    {
        $this->date_fermeture = $date_fermeture;

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

    /**
     * @return Collection<int, ContactsEntreprise>
     */
    public function getContactsEntreprises(): Collection
    {
        return $this->contactsEntreprises;
    }

    public function addContactsEntreprise(ContactsEntreprise $contactsEntreprise): static
    {
        if (!$this->contactsEntreprises->contains($contactsEntreprise)) {
            $this->contactsEntreprises->add($contactsEntreprise);
            $contactsEntreprise->addRefOffre($this);
        }

        return $this;
    }

    public function removeContactsEntreprise(ContactsEntreprise $contactsEntreprise): static
    {
        if ($this->contactsEntreprises->removeElement($contactsEntreprise)) {
            $contactsEntreprise->removeRefOffre($this);
        }

        return $this;
    }

    public function getRefTypesOffre(): ?TypesOffres
    {
        return $this->refTypesOffre;
    }

    public function setRefTypesOffre(?TypesOffres $refTypesOffre): static
    {
        $this->refTypesOffre = $refTypesOffre;

        return $this;
    }

    public function getRefCreateur(): ?User
    {
        return $this->refCreateur;
    }

    public function setRefCreateur(?User $refCreateur): static
    {
        $this->refCreateur = $refCreateur;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getRefUser(): Collection
    {
        return $this->refUser;
    }

    public function addRefUser(User $refUser): static
    {
        if (!$this->refUser->contains($refUser)) {
            $this->refUser->add($refUser);
        }

        return $this;
    }

    public function removeRefUser(User $refUser): static
    {
        $this->refUser->removeElement($refUser);

        return $this;
    }
}
