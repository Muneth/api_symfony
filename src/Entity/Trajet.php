<?php

namespace App\Entity;

use App\Repository\TrajetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrajetRepository::class)]
class Trajet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $kms = null;

    #[ORM\ManyToOne(inversedBy: 'trajets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $villedepart = null;

    #[ORM\ManyToOne(inversedBy: 'trajets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $villearrive = null;

    #[ORM\ManyToOne(inversedBy: 'trajetsconduit')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personne $conducteur = null;

    #[ORM\ManyToMany(targetEntity: Personne::class, mappedBy: 'trajets')]
    private Collection $personnesUser;

    public function __construct()
    {
        $this->personnesUser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getKms(): ?int
    {
        return $this->kms;
    }

    public function setKms(int $kms): self
    {
        $this->kms = $kms;

        return $this;
    }

    public function getVilledepart(): ?Ville
    {
        return $this->villedepart;
    }

    public function setVilledepart(?Ville $villedepart): self
    {
        $this->villedepart = $villedepart;

        return $this;
    }

    public function getVillearrive(): ?Ville
    {
        return $this->villearrive;
    }

    public function setVillearrive(?Ville $villearrive): self
    {
        $this->villearrive = $villearrive;

        return $this;
    }

    public function getConducteur(): ?Personne
    {
        return $this->conducteur;
    }

    public function setConducteur(?Personne $conducteur): self
    {
        $this->conducteur = $conducteur;

        return $this;
    }

    /**
     * @return Collection<int, Personne>
     */
    public function getPersonnesUser(): Collection
    {
        return $this->personnesUser;
    }

    public function addPersonnesUser(Personne $personnesUser): self
    {
        if (!$this->personnesUser->contains($personnesUser)) {
            $this->personnesUser->add($personnesUser);
            $personnesUser->addTrajet($this);
        }

        return $this;
    }

    public function removePersonnesUser(Personne $personnesUser): self
    {
        if ($this->personnesUser->removeElement($personnesUser)) {
            $personnesUser->removeTrajet($this);
        }

        return $this;
    }
}
