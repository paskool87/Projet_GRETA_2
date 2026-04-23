<?php

namespace App\Entity;

use App\Repository\TutoratRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TutoratRepository::class)]
class Tutorat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tutorats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $tuteur = null;

    #[ORM\ManyToOne(inversedBy: 'tutorats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alternant $alternant = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $date_fin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

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

    public function getTuteur(): ?Utilisateur
    {
        return $this->tuteur;
    }

    public function setTuteur(?Utilisateur $tuteur): static
    {
        $this->tuteur = $tuteur;

        return $this;
    }

    public function getAlternant(): ?Alternant
    {
        return $this->alternant;
    }

    public function setAlternant(?Alternant $alternant): static
    {
        $this->alternant = $alternant;

        return $this;
    }
}
