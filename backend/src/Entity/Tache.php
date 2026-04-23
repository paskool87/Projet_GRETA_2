<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;


#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fiche $fiche = null;


    #[ORM\Column(length: 250)]
    #[Groups(['user:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['user:read'])]
    private ?DateTime $date_tache = null;

    #[ORM\Column(nullable: true)]
    private ?bool $autonomie = null;

    #[ORM\Column(nullable: true)]
    private ?bool $observation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $surveille = null;

    #[ORM\Column(nullable: true)]
    private ?bool $absence = null;

    #[ORM\Column(nullable: true)]
    private ?bool $ferie = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['user:read'])]
    private ?DateTime $date_creation = null;

    public function __construct()
    {
        $this->date_creation = new DateTime('now');
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


    public function getDateTache(): ?DateTime
    {
        return $this->date_tache;
    }

    public function setDateTache(DateTime $date_tache): static
    {
        $this->date_tache = $date_tache;

        return $this;
    }

    public function isAutonomie(): ?bool
    {
        return $this->autonomie;
    }

    public function setAutonomie(?bool $autonomie): static
    {
        $this->autonomie = $autonomie;

        return $this;
    }

    public function isObservation(): ?bool
    {
        return $this->observation;
    }

    public function setObservation(?bool $observation): static
    {
        $this->observation = $observation;

        return $this;
    }

    public function isSurveille(): ?bool
    {
        return $this->surveille;
    }

    public function setSurveille(?bool $surveille): static
    {
        $this->surveille = $surveille;

        return $this;
    }

    public function isAbsence(): ?bool
    {
        return $this->absence;
    }

    public function setAbsence(?bool $absence): static
    {
        $this->absence = $absence;

        return $this;
    }

    public function isFerie(): ?bool
    {
        return $this->ferie;
    }

    public function setFerie(?bool $ferie): static
    {
        $this->ferie = $ferie;

        return $this;
    }

    public function getDateCreation(): ?DateTime
    {
        return $this->date_creation;
    }

    public function setDateCreation(?DateTime $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    #[Groups(['user:read'])]
    public function getCategorie(): ?string
    {
        $m = "pas de status";
        if ($this->autonomie) {
            $m = "autonomie";
        } elseif ($this->surveille) {
            $m = "surveille";
        } elseif ($this->observation) {
            $m = "observation";
        } elseif ($this->ferie) {
            $m = "ferie";
        } elseif ($this->absence) {
            $m = "absence";
        }
        return $m;
    }

    public function setCategorie(string $catNom): void
    {
        $this->autonomie = false;
        $this->surveille = false;
        $this->observation = false;
        $this->ferie = false;
        $this->absence = false;
        match (strtolower($catNom)) {
            "autonomie" => $this->setAutonomie(true),
            "surveille" => $this->setSurveille(true),
            "observation" => $this->setObservation(true),
            "ferie" => $this->setFerie(true),
            "absence" => $this->setAbsence(true),
            default => null
        };
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getFiche(): ?Fiche
    {
        return $this->fiche;
    }

    public function setFiche(?Fiche $fiche): static
    {
        $this->fiche = $fiche;

        return $this;
    }
}
