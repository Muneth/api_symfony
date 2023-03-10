<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
class Personne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $tel = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'personne', targetEntity: Voiture::class)]
    private Collection $voitures;

    #[ORM\OneToMany(mappedBy: 'conducteur', targetEntity: Trajet::class, orphanRemoval: true)]
    private Collection $trajetsconduit;

    #[ORM\ManyToMany(targetEntity: Trajet::class, inversedBy: 'personnesUser')]
    private Collection $trajets;

    public function __construct()
    {
        $this->voitures = new ArrayCollection();
        $this->trajetsconduit = new ArrayCollection();
        $this->trajets = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTel(): ?int
    {
        return $this->tel;
    }

    public function setTel(int $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Voiture>
     */
    public function getVoitures(): Collection
    {
        return $this->voitures;
    }

    public function addVoiture(Voiture $voiture): self
    {
        if (!$this->voitures->contains($voiture)) {
            $this->voitures->add($voiture);
            $voiture->setPersonne($this);
        }

        return $this;
    }

    public function removeVoiture(Voiture $voiture): self
    {
        if ($this->voitures->removeElement($voiture)) {
            // set the owning side to null (unless already changed)
            if ($voiture->getPersonne() === $this) {
                $voiture->setPersonne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trajet>
     */
    public function getTrajetsconduit(): Collection
    {
        return $this->trajetsconduit;
    }

    public function addTrajetsconduit(Trajet $trajetsconduit): self
    {
        if (!$this->trajetsconduit->contains($trajetsconduit)) {
            $this->trajetsconduit->add($trajetsconduit);
            $trajetsconduit->setConducteur($this);
        }

        return $this;
    }

    public function removeTrajetsconduit(Trajet $trajetsconduit): self
    {
        if ($this->trajetsconduit->removeElement($trajetsconduit)) {
            // set the owning side to null (unless already changed)
            if ($trajetsconduit->getConducteur() === $this) {
                $trajetsconduit->setConducteur(null);
            }
        }

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
        }

        return $this;
    }

    public function removeTrajet(Trajet $trajet): self
    {
        $this->trajets->removeElement($trajet);

        return $this;
    }
}
