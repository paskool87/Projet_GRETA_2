<?php

namespace App\Entity;

use App\Enum\Role;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use function PHPSTORM_META\type;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    private ?string $nom = null;

    #[ORM\Column(length: 45)]
    private ?string $prenom = null;

    #[ORM\Column(length: 64)]
    private ?string $email = null;

    #[ORM\Column(length: 80)]
    private ?string $mot_de_passe = null;

    #[ORM\Column(length:240)]
    private ?string $role;

    #[ORM\Column]
    private ?bool $actif = null;

    

    /**
     * @var Collection<int, Tutorat>
     */
    #[ORM\OneToMany(targetEntity: Tutorat::class, mappedBy: 'tuteur_id')]
    private Collection $tutorats;

    /**
     * @var Collection<int, SuiviPedagogique>
     */
    #[ORM\OneToMany(targetEntity: SuiviPedagogique::class, mappedBy: 'professeur')]
    private Collection $suiviPedagogiques;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $date_creation = null;

    public function __construct()
    {
        $this->tutorats = new ArrayCollection();
        $this->suiviPedagogiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): static
    {
        $this->mot_de_passe = $mot_de_passe;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection<int, Tutorat>
     */
    public function getTutorats(): Collection
    {
        return $this->tutorats;
    }

    public function addTutorat(Tutorat $tutorat): static
    {
        if (!$this->tutorats->contains($tutorat)) {
            $this->tutorats->add($tutorat);
            $tutorat->setTuteurId($this);
        }

        return $this;
    }

    public function removeTutorat(Tutorat $tutorat): static
    {
        if ($this->tutorats->removeElement($tutorat)) {
            // set the owning side to null (unless already changed)
            if ($tutorat->getTuteurId() === $this) {
                $tutorat->setTuteurId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SuiviPedagogique>
     */
    public function getSuiviPedagogiques(): Collection
    {
        return $this->suiviPedagogiques;
    }

    public function addSuiviPedagogique(SuiviPedagogique $suiviPedagogique): static
    {
        if (!$this->suiviPedagogiques->contains($suiviPedagogique)) {
            $this->suiviPedagogiques->add($suiviPedagogique);
            $suiviPedagogique->setProfesseur($this);
        }

        return $this;
    }

    public function removeSuiviPedagogique(SuiviPedagogique $suiviPedagogique): static
    {
        if ($this->suiviPedagogiques->removeElement($suiviPedagogique)) {
            // set the owning side to null (unless already changed)
            if ($suiviPedagogique->getProfesseur() === $this) {
                $suiviPedagogique->setProfesseur(null);
            }
        }

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTime $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }
}