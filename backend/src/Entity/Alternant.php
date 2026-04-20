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
    #[Groups(['admin'])]
    private ?int $id = null;

    /**
     * @var Collection<int, Fiche>
     */
    #[ORM\OneToMany(targetEntity: Fiche::class, mappedBy: 'alternant_id')]
    private Collection $fiches;

    /**
     * @var Collection<int, Tutorat>
     */
    #[ORM\OneToMany(targetEntity: Tutorat::class, mappedBy: 'alternant_id')]
    private Collection $tutorats;

    /**
     * @var Collection<int, SuiviPedagogique>
     */
    #[ORM\OneToMany(targetEntity: SuiviPedagogique::class, mappedBy: 'alternant_id')]
    private Collection $suiviPedagogiques;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['admin'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur_id = null;

    #[ORM\ManyToOne(inversedBy: 'alternants')]
    #[Groups(['admin'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formation $formation_id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['admin'])]
    private ?\DateTime $date_creation = null;

    #[ORM\Column]
    #[Groups(['admin'])]
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
            $fich->setAlternantId($this);
        }

        return $this;
    }

    public function removeFich(Fiche $fich): static
    {
        if ($this->fiches->removeElement($fich)) {
            // set the owning side to null (unless already changed)
            if ($fich->getAlternantId() === $this) {
                $fich->setAlternantId(null);
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
            $suiviPedagogique->setAlternantId($this);
        }

        return $this;
    }

    public function removeSuiviPedagogique(SuiviPedagogique $suiviPedagogique): static
    {
        if ($this->suiviPedagogiques->removeElement($suiviPedagogique)) {
            // set the owning side to null (unless already changed)
            if ($suiviPedagogique->getAlternantId() === $this) {
                $suiviPedagogique->setAlternantId(null);
            }
        }

        return $this;
    }

    public function getUtilisateurId(): ?Utilisateur
    {
        return $this->utilisateur_id;
    }

    public function setUtilisateurId(Utilisateur $utilisateur_id): static
    {
        $this->utilisateur_id = $utilisateur_id;

        return $this;
    }

    public function getFormationId(): ?Formation
    {
        return $this->formation_id;
    }

    public function setFormationId(?Formation $formation_id): static
    {
        $this->formation_id = $formation_id;

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
            $tutorat->setAlternantId($this);
        }

        return $this;
    }

    public function removeTutorat(Tutorat $tutorat): static
    {
        if ($this->tutorats->removeElement($tutorat)) {
            // set the owning side to null (unless already changed)
            if ($tutorat->getAlternantId() === $this) {
                $tutorat->setAlternantId(null);
            }
        }

        return $this;
    }
}
