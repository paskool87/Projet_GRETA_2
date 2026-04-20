<?php

namespace App\Entity;

use App\Enum\Jour;
use App\Repository\TacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;


#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['admin'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fiche $fiche_id = null;


    #[ORM\Column(length: 250)]
    #[Groups(['admin'])]
    private ?string $tache_acomplie = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['admin'])]
    private ?\DateTime $date_tache = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['admin'])]
    private ?bool $autonomie = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['admin'])]
    private ?bool $observation = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['admin'])]
    private ?bool $surveille = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['admin'])]
    private ?bool $absence = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['admin'])]
    private ?bool $ferie = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['admin'])]
    private ?\DateTime $date_creation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getFicheId(): ?Fiche
    {
        return $this->fiche_id;
    }

    public function setFicheId(?Fiche $fiche_id): static
    {
        $this->fiche_id = $fiche_id;

        return $this;
    }


    public function getDateTache(): ?\DateTime
    {
        return $this->date_tache;
    }

    public function setDateTache(\DateTime $date_tache): static
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

    public function setObservation(bool $observation): static
    {
        $this->observation = $observation;

        return $this;
    }

    public function isSurveille(): ?bool
    {
        return $this->surveille;
    }

    public function setSurveille(bool $surveille): static
    {
        $this->surveille = $surveille;

        return $this;
    }

    public function isAbsence(): ?bool
    {
        return $this->absence;
    }

    public function setAbsence(bool $absence): static
    {
        $this->absence = $absence;

        return $this;
    }

    public function isFerie(): ?bool
    {
        return $this->ferie;
    }

    public function setFerie(bool $ferie): static
    {
        $this->ferie = $ferie;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTime $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getTacheAcomplie(): ?string
    {
        return $this->tache_acomplie;
    }

    public function setTacheAcomplie(string $tache_acomplie): static
    {
        $this->tache_acomplie = $tache_acomplie;

        return $this;
    }
}
