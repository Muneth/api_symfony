<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $cp = null;

    #[ORM\OneToMany(mappedBy: 'villedepart', targetEntity: Trajet::class)]
    private Collection $trajets;

    #[ORM\OneToMany(mappedBy: 'villearrive', targetEntity: Trajet::class)]
    private Collection $trajetss;

    public function __construct()
    {
        $this->trajets = new ArrayCollection();
        $this->trajetss = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * @return Collection<int, Trajet>
     */
    public function getTrajets(): Collection
    {
        return $this->trajets;
    }

    public function addTrajet(Trajet $trajet): self
    {
        if (!$this->trajets->contains($trajet)) {
            $this->trajets->add($trajet);
            $trajet->setVilledepart($this);
        }

        return $this;
    }

    public function removeTrajet(Trajet $trajet): self
    {
        if ($this->trajets->removeElement($trajet)) {
            // set the owning side to null (unless already changed)
            if ($trajet->getVilledepart() === $this) {
                $trajet->setVilledepart(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trajet>
     */
    public function getTrajetss(): Collection
    {
        return $this->trajetss;
    }

    public function addTrajets(Trajet $trajets): self
    {
        if (!$this->trajetss->contains($trajets)) {
            $this->trajetss->add($trajets);
            $trajets->setVillearrive($this);
        }

        return $this;
    }

    public function removeTrajets(Trajet $trajets): self
    {
        if ($this->trajetss->removeElement($trajets)) {
            // set the owning side to null (unless already changed)
            if ($trajets->getVillearrive() === $this) {
                $trajets->setVillearrive(null);
            }
        }

        return $this;
    }
}
