<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    #[Groups(['user:read'])]
    private ?string $nom_formation = null;


    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    #[Groups(['user:read'])]
    private ?string $session = null;

    /**
     * @var Collection<int, SuiviPedagogique>
     */
    #[ORM\OneToMany(targetEntity: SuiviPedagogique::class, mappedBy: 'formation')]
    private Collection $suiviPedagogiques;

    /**
     * @var Collection<int, Alternant>
     */
    #[ORM\OneToMany(targetEntity: Alternant::class, mappedBy: 'formation')]
    private Collection $alternants;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $date_fin = null;

    public function __construct()
    {
        $this->suiviPedagogiques = new ArrayCollection();
        $this->alternants = new ArrayCollection();
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

    public function getNomFormation(): ?string
    {
        return $this->nom_formation;
    }

    public function setNomFormation(string $nom_formation): static
    {
        $this->nom_formation = $nom_formation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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
            $suiviPedagogique->setFormation($this);
        }

        return $this;
    }

    public function removeSuiviPedagogique(SuiviPedagogique $suiviPedagogique): static
    {
        if ($this->suiviPedagogiques->removeElement($suiviPedagogique)) {
            // set the owning side to null (unless already changed)
            if ($suiviPedagogique->getFormation() === $this) {
                $suiviPedagogique->setFormation(null);
            }
        }

        return $this;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(string $session): static
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return Collection<int, Alternant>
     */
    public function getAlternants(): Collection
    {
        return $this->alternants;
    }

    public function addAlternant(Alternant $alternant): static
    {
        if (!$this->alternants->contains($alternant)) {
            $this->alternants->add($alternant);
            $alternant->setFormation($this);
        }

        return $this;
    }

    public function removeAlternant(Alternant $alternant): static
    {
        if ($this->alternants->removeElement($alternant)) {
            // set the owning side to null (unless already changed)
            if ($alternant->getFormation() === $this) {
                $alternant->setFormation(null);
            }
        }

        return $this;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTime $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTime $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }
}
