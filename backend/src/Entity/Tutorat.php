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
    private ?Utilisateur $tuteur_id = null;

    #[ORM\ManyToOne(inversedBy: 'tutorats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alternant $alternant_id = null;

    #[ORM\Column]
    #[Groups(['admin'])]
    private ?bool $actif = null;

    #[Groups(['admin'])]
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $date_debut = null;

    #[Groups(['admin'])]
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

    public function getTuteurId(): ?Utilisateur
    {
        return $this->tuteur_id;
    }

    public function setTuteurId(?Utilisateur $tuteur_id): static
    {
        $this->tuteur_id = $tuteur_id;

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
}
