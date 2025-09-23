<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;



    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cv = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $specialite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cpEntreprise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rueEntreprise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $villeEntreprise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pays = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $formation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $EstValide = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $date_creation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nb_rueEntreprise = null;

    /**
     * @var Collection<int, Evenements>
     */

    /**
     * @var Collection<int, Commentaires>
     */
    #[ORM\OneToMany(targetEntity: Commentaires::class, mappedBy: 'refUser', orphanRemoval: true)]
    private Collection $commentaires;

    /**
     * @var Collection<int, Sujets>
     */
    #[ORM\OneToMany(targetEntity: Sujets::class, mappedBy: 'refUser')]
    private Collection $sujets;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Etablissements $refEtablissement = null;

    /**
     * @var Collection<int, ContactsEntreprise>
     */
    #[ORM\OneToMany(targetEntity: ContactsEntreprise::class, mappedBy: 'refEntreprise', orphanRemoval: true)]
    private Collection $contactsEntreprises;

    /**
     * @var Collection<int, Offres>
     */
    #[ORM\OneToMany(targetEntity: Offres::class, mappedBy: 'refCreateur', orphanRemoval: true)]
    private Collection $offres;

    /**
     * @var Collection<int, Offres>
     */
    #[ORM\ManyToMany(targetEntity: Offres::class, mappedBy: 'refUser')]
    private Collection $candidatures;

    /**
     * @var Collection<int, UserEvenement>
     */
    #[ORM\OneToMany(targetEntity: UserEvenement::class, mappedBy: 'refUser', orphanRemoval: true)]
    private Collection $userEvenements;

    private $evenementsResponsable;

    public function __construct()
    {
        $this->evenementsResponsable = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->sujets = new ArrayCollection();
        $this->contactsEntreprises = new ArrayCollection();
        $this->offres = new ArrayCollection();
        $this->candidatures = new ArrayCollection();
        $this->userEvenements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    // Ne sera pas persisté en BDD, juste pour le formulaire
    public function getRole(): ?string
    {
        return $this->roles[0] ?? null;
    }

    public function setRole(string $role): self
    {
        $this->roles = [$role];
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }





    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(?string $cv): static
    {
        $this->cv = $cv;

        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(?string $specialite): static
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getCpEntreprise(): ?string
    {
        return $this->cpEntreprise;
    }

    public function setCpEntreprise(?string $cpEntreprise): static
    {
        $this->cpEntreprise = $cpEntreprise;

        return $this;
    }

    public function getRueEntreprise(): ?string
    {
        return $this->rueEntreprise;
    }

    public function setRueEntreprise(?string $rueEntreprise): static
    {
        $this->rueEntreprise = $rueEntreprise;

        return $this;
    }

    public function getVilleEntreprise(): ?string
    {
        return $this->villeEntreprise;
    }

    public function setVilleEntreprise(?string $villeEntreprise): static
    {
        $this->villeEntreprise = $villeEntreprise;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getFormation(): ?string
    {
        return $this->formation;
    }

    public function setFormation(?string $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    public function isEstValide(): ?bool
    {
        return $this->EstValide;
    }

    public function setEstValide(?bool $EstValide): static
    {
        $this->EstValide = $EstValide;

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

    public function getNbRueEntreprise(): ?string
    {
        return $this->nb_rueEntreprise;
    }

    public function setNbRueEntreprise(?string $nb_rueEntreprise): static
    {
        $this->nb_rueEntreprise = $nb_rueEntreprise;

        return $this;
    }

    /**
     * @return Collection<int, Evenements>
     */



    /**
     * @return Collection<int, Evenements>
     */
    public function getEvenementsResponsable(): Collection
    {
        return $this->evenementsResponsable;
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
            $commentaire->setRefUser($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaires $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getRefUser() === $this) {
                $commentaire->setRefUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sujets>
     */
    public function getSujets(): Collection
    {
        return $this->sujets;
    }

    public function addSujet(Sujets $sujet): static
    {
        if (!$this->sujets->contains($sujet)) {
            $this->sujets->add($sujet);
            $sujet->setRefUser($this);
        }

        return $this;
    }

    public function removeSujet(Sujets $sujet): static
    {
        if ($this->sujets->removeElement($sujet)) {
            // set the owning side to null (unless already changed)
            if ($sujet->getRefUser() === $this) {
                $sujet->setRefUser(null);
            }
        }

        return $this;
    }

    public function getRefEtablissement(): ?Etablissements
    {
        return $this->refEtablissement;
    }

    public function setRefEtablissement(?Etablissements $refEtablissement): static
    {
        $this->refEtablissement = $refEtablissement;

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
            $contactsEntreprise->setRefEntreprise($this);
        }

        return $this;
    }

    public function removeContactsEntreprise(ContactsEntreprise $contactsEntreprise): static
    {
        if ($this->contactsEntreprises->removeElement($contactsEntreprise)) {
            // set the owning side to null (unless already changed)
            if ($contactsEntreprise->getRefEntreprise() === $this) {
                $contactsEntreprise->setRefEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Offres>
     */
    public function getOffres(): Collection
    {
        return $this->offres;
    }

    public function addOffre(Offres $offre): static
    {
        if (!$this->offres->contains($offre)) {
            $this->offres->add($offre);
            $offre->setRefCreateur($this);
        }

        return $this;
    }

    public function removeOffre(Offres $offre): static
    {
        if ($this->offres->removeElement($offre)) {
            // set the owning side to null (unless already changed)
            if ($offre->getRefCreateur() === $this) {
                $offre->setRefCreateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Offres>
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Offres $candidature): static
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures->add($candidature);
            $candidature->addRefUser($this);
        }

        return $this;
    }

    public function removeCandidature(Offres $candidature): static
    {
        if ($this->candidatures->removeElement($candidature)) {
            $candidature->removeRefUser($this);
        }

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
            $userEvenement->setRefUser($this);
        }

        return $this;
    }

    public function removeUserEvenement(UserEvenement $userEvenement): static
    {
        if ($this->userEvenements->removeElement($userEvenement)) {
            // set the owning side to null (unless already changed)
            if ($userEvenement->getRefUser() === $this) {
                $userEvenement->setRefUser(null);
            }
        }

        return $this;
    }
}
