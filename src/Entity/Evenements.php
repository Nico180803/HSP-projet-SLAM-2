<?php

namespace App\Entity;

use App\Repository\EvenementsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementsRepository::class)]
class Evenements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nb_rue = null;

    #[ORM\Column]
    private ?int $nb_places = null;

    #[ORM\Column]
    private ?int $nb_places_dispo = null;

    #[ORM\Column]
    private ?bool $est_valide = null;

    #[ORM\ManyToOne(inversedBy: 'evenements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypesEvenements $refTypesEvenement = null;



    /**
     * @var Collection<int, UserEvenement>
     */
    #[ORM\OneToMany(targetEntity: UserEvenement::class, mappedBy: 'refEvenement', orphanRemoval: true)]
    private Collection $userEvenements;

    public function __construct()
    {
        $this->inscrits = new ArrayCollection();
        $this->responsables = new ArrayCollection();
        $this->userEvenements = new ArrayCollection();
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

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): static
    {
        $this->rue = $rue;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(?string $cp): static
    {
        $this->cp = $cp;

        return $this;
    }

    public function getNbRue(): ?string
    {
        return $this->nb_rue;
    }

    public function setNbRue(?string $nb_rue): static
    {
        $this->nb_rue = $nb_rue;

        return $this;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nb_places;
    }

    public function setNbPlaces(int $nb_places): static
    {
        $this->nb_places = $nb_places;

        return $this;
    }

    public function getNbPlacesDispo(): ?int
    {
        return $this->nb_places_dispo;
    }

    public function setNbPlacesDispo(int $nb_places_dispo): static
    {
        $this->nb_places_dispo = $nb_places_dispo;

        return $this;
    }

    public function isEstValide(): ?bool
    {
        return $this->est_valide;
    }

    public function setEstValide(bool $est_valide): static
    {
        $this->est_valide = $est_valide;

        return $this;
    }

    public function getRefTypesEvenement(): ?TypesEvenements
    {
        return $this->refTypesEvenement;
    }

    public function setRefTypesEvenement(?TypesEvenements $refTypesEvenement): static
    {
        $this->refTypesEvenement = $refTypesEvenement;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getinscrits(): Collection
    {
        return $this->inscrits;
    }

    public function addUser(User $user): static
    {
        if (!$this->inscrits->contains($user)) {
            $this->inscrits->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->inscrits->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getResponsables(): Collection
    {
        return $this->responsables;
    }

    public function addResponsable(User $responsable): static
    {
        if (!$this->responsables->contains($responsable)) {
            $this->responsables->add($responsable);
        }

        return $this;
    }

    public function removeResponsable(User $responsable): static
    {
        $this->responsables->removeElement($responsable);

        return $this;
    }

    /**
     * @return Collection<int, UserEvenement>
     */
    public function getUserEvenements(): Collection
    {
        return $this->userEvenements;
    }

    public function addUserEvenement(UserEvenement $userEvenement): static
    {
        if (!$this->userEvenements->contains($userEvenement)) {
            $this->userEvenements->add($userEvenement);
            $userEvenement->setRefEvenement($this);
        }

        return $this;
    }

    public function removeUserEvenement(UserEvenement $userEvenement): static
    {
        if ($this->userEvenements->removeElement($userEvenement)) {
            // set the owning side to null (unless already changed)
            if ($userEvenement->getRefEvenement() === $this) {
                $userEvenement->setRefEvenement(null);
            }
        }

        return $this;
    }
}
