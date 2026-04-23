<?php

namespace App\Entity;

use App\Repository\AlternantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlternantRepository::class)]
class Alternant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    /**
     * @var Collection<int, Fiche>
     */
    #[ORM\OneToMany(targetEntity: Fiche::class, mappedBy: 'alternant')]
    private Collection $fiches;

    /**
     * @var Collection<int, Tutorat>
     */
    #[ORM\OneToMany(targetEntity: Tutorat::class, mappedBy: 'alternant')]
    private Collection $tutorats;

    /**
     * @var Collection<int, SuiviPedagogique>
     */
    #[ORM\OneToMany(targetEntity: SuiviPedagogique::class, mappedBy: 'alternant')]
    private Collection $suiviPedagogiques;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read'])]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'alternants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formation $formation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['user:read'])]
    private ?\DateTime $date_creation = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?bool $actif = null;

    public function __construct()
    {
        $this->fiches = new ArrayCollection();
        $this->suiviPedagogiques = new ArrayCollection();
        $this->tutorats = new ArrayCollection();
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

    /**
     * @return Collection<int, Fiche>
     */
    public function getFiches(): Collection
    {
        return $this->fiches;
    }

    public function addFich(Fiche $fich): static
    {
        if (!$this->fiches->contains($fich)) {
            $this->fiches->add($fich);
            $fich->setAlternant($this);
        }

        return $this;
    }

    public function removeFich(Fiche $fich): static
    {
        if ($this->fiches->removeElement($fich)) {
            // set the owning side to null (unless already changed)
            if ($fich->getAlternant() === $this) {
                $fich->setAlternant(null);
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
            $suiviPedagogique->setAlternant($this);
        }

        return $this;
    }

    public function removeSuiviPedagogique(SuiviPedagogique $suiviPedagogique): static
    {
        if ($this->suiviPedagogiques->removeElement($suiviPedagogique)) {
            // set the owning side to null (unless already changed)
            if ($suiviPedagogique->getAlternant() === $this) {
                $suiviPedagogique->setAlternant(null);
            }
        }

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getFormationId(): ?Formation
    {
        return $this->formation;
    }

    public function setFormationId(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
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
            $tutorat->setAlternant($this);
        }

        return $this;
    }

    public function removeTutorat(Tutorat $tutorat): static
    {
        if ($this->tutorats->removeElement($tutorat)) {
            // set the owning side to null (unless already changed)
            if ($tutorat->getAlternant() === $this) {
                $tutorat->setAlternant(null);
            }
        }

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }
}
