<?php

namespace App\Entity;

use App\Repository\SuiviPedagogiqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuiviPedagogiqueRepository::class)]
class SuiviPedagogique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'suiviPedagogiques')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alternant $alternant_id = null;

    #[ORM\ManyToOne(inversedBy: 'suiviPedagogiques')]
    private ?Utilisateur $professeur = null;

    #[ORM\ManyToOne(inversedBy: 'suiviPedagogiques')]
    private ?Formation $formation_id = null;

    #[ORM\Column]
    private ?bool $est_principal = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $date_fin = null;

    #[ORM\Column]
    private ?bool $actif = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getAlternantId(): ?Alternant
    {
        return $this->alternant_id;
    }

    public function setAlternantId(?Alternant $alternant_id): static
    {
        $this->alternant_id = $alternant_id;

        return $this;
    }

    public function getProfesseur(): ?Utilisateur
    {
        return $this->professeur;
    }

    public function setProfesseur(?Utilisateur $professeur): static
    {
        $this->professeur = $professeur;

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

    public function isEstPrincipal(): ?bool
    {
        return $this->est_principal;
    }

    public function setEstPrincipal(bool $est_principal): static
    {
        $this->est_principal = $est_principal;

        return $this;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->date_debut;
    }

    public function setDateDebut(?\DateTime $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTime $date_fin): static
    {
        $this->date_fin = $date_fin;

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
}
