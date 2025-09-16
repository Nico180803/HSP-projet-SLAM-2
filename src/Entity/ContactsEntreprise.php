<?php

namespace App\Entity;

use App\Repository\ContactsEntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactsEntrepriseRepository::class)]
class ContactsEntreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $tel = null;

    #[ORM\Column(length: 255)]
    private ?string $mail = null;

    #[ORM\ManyToOne(inversedBy: 'contactsEntreprises')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $refEntreprise = null;

    /**
     * @var Collection<int, Offres>
     */
    #[ORM\ManyToMany(targetEntity: Offres::class, inversedBy: 'contactsEntreprises')]
    private Collection $refOffre;

    public function __construct()
    {
        $this->refOffre = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getRefEntreprise(): ?User
    {
        return $this->refEntreprise;
    }

    public function setRefEntreprise(?User $refEntreprise): static
    {
        $this->refEntreprise = $refEntreprise;

        return $this;
    }

    /**
     * @return Collection<int, Offres>
     */
    public function getRefOffre(): Collection
    {
        return $this->refOffre;
    }

    public function addRefOffre(Offres $refOffre): static
    {
        if (!$this->refOffre->contains($refOffre)) {
            $this->refOffre->add($refOffre);
        }

        return $this;
    }

    public function removeRefOffre(Offres $refOffre): static
    {
        $this->refOffre->removeElement($refOffre);

        return $this;
    }
}
