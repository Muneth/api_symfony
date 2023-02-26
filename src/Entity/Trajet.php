<?php

namespace App\Entity;

use App\Repository\TrajetRepository;
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
}
