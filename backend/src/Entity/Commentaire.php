<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private ?string $commentaire = null;


    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?utilisateur $auteur_id = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fiche $fiche_id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
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

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getFicheId(): ?Fiche
    {
        return $this->fiche_id;
    }

    public function getAuteurId(): ?utilisateur
    {
        return $this->auteur_id;
    }

    public function setAuteurId(?utilisateur $auteur_id): static
    {
        $this->auteur_id = $auteur_id;

        return $this;
    }

    public function setFicheId(?Fiche $fiche_id): static
    {
        $this->fiche_id = $fiche_id;

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
}