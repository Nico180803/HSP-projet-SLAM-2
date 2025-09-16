<?php

namespace App\Entity;

use App\Repository\TypesEvenementsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypesEvenementsRepository::class)]
class TypesEvenements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, Evenements>
     */
    #[ORM\OneToMany(targetEntity: Evenements::class, mappedBy: 'refTypesEvenement')]
    private Collection $Evenements;

    public function __construct()
    {
        $this->Evenements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Evenements>
     */
    public function getEvenements(): Collection
    {
        return $this->Evenements;
    }

    public function addEvenemenent(Evenements $evenemenent): static
    {
        if (!$this->Evenements->contains($evenemenent)) {
            $this->Evenements->add($evenemenent);
            $evenemenent->setRefTypesEvenement($this);
        }

        return $this;
    }

    public function removeEvenemenent(Evenements $evenemenent): static
    {
        if ($this->Evenements->removeElement($evenemenent)) {
            // set the owning side to null (unless already changed)
            if ($evenemenent->getRefTypesEvenement() === $this) {
                $evenemenent->setRefTypesEvenement(null);
            }
        }

        return $this;
    }
}
